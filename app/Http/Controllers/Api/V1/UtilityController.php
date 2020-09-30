<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\Email;
use App\Models\Text;
use App\Models\Customer;
use App\Models\User;
use App\Models\Location;
use App\Models\Locationuser;
use App\Models\Googleplace;

// Jobs
use App\Jobs\SendText;
use App\Jobs\SendEmail;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Other
use Hash;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Cache;
use DB;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


//I don't believe any of this is used.
class UtilityController extends Controller
{
    

    //never used. There is no Customer Model, and no Customer table
    public function import_customers(Request $request)
    {
        $messages = [];

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xls,xlsx',
        ], $messages);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not import customers.', $validator->errors());
        }

        $failed_import_message = '<p>The file you uploaded could not be parsed. 
                This may be because it is in an old file format or it has some corrupted meta data. 
                You could try copying and pasting the data into the Client Template spread sheet 
                and then uploading it again.</p>';

        try {
            $data = Excel::selectSheetsByIndex(0)->load($request->file)->get();
        }
        catch (\Exception $e) {
            $validator->after(function ($validator) {
                $validator->errors()->add('file', $failed_import_message);
            });
        }


        if (!$data->count()) {
            $validator->after(function ($validator) {
                $validator->errors()->add('file', $failed_import_message);
            });
        }

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not import customers.', $validator->errors());
        }

        // Delete existing customers
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('customers')->where('user_id', $this->user()->id)->delete();
        DB::table('emails')->where('user_id', $this->user()->id)->whereNotNull('customer_id')->delete();
        DB::table('messages')->where('user_id', $this->user()->id)->whereNotNull('customer_id')->delete();
        DB::table('texts')->where('user_id', $this->user()->id)->whereNotNull('customer_id')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $skipped = [];
        $count = 0;
        foreach ($data as $index => $row) {

            // 0 => "first_name"
            // 1 => "last_name"
            // 2 => "email"
            // 3 => "phone"
            // 5 => "notes"            
            try {
                $customer = new Customer;
                $customer->user_id = $this->user()->id;
                $customer->key = str_random(20);
                $customer->first_name = $row->first_name ?: '';
                $customer->last_name = $row->last_name ?: '';
                $customer->email = $row->email ?: '';
                $customer->phone = databasePhone($row->phone);
                $customer->notes = $row->notes ?: '';
                $customer->save();
                $count++;
            }
            catch (Exception $e) {
                $skipped[$index] = $row;
            }
            
        }

        return $this->response->array([
            'message' => 'Imported Successfully',
            'status_code' => 200,
            'count' => $count,
            'skipped' => $skipped
        ]);
    }


    //Not used.
    public function import_users(Request $request)
    {
        $messages = [];

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xls,xlsx,csv',
        ], $messages);

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not import users.', $validator->errors());
        }

        $failed_import_message = '<p>The file you uploaded could not be parsed. 
                This may be because it is in an old file format or it has some corrupted meta data. 
                You could try copying and pasting the data into the Client Template spread sheet 
                and then uploading it again.</p>';

        try {
            $data = Excel::selectSheetsByIndex(0)->load($request->file)->get();
        }
        catch (\Exception $e) {
            $validator->after(function ($validator) {
                $validator->errors()->add('file', $failed_import_message);
            });
        }


        if (!$data->count()) {
            $validator->after(function ($validator) {
                $validator->errors()->add('file', $failed_import_message);
            });
        }

        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not import users.', $validator->errors());
        }

        $skipped = [];
        $count = 0;
        foreach ($data as $index => $row) {

            // 0 => "business_name"
            // 1 => "location_name_optional"
            // 2 => "street_address"
            // 3 => "secondary_address"
            // 4 => "city"
            // 5 => "state"
            // 6 => "zip"
            // 7 => "country"
            // 8 => "phone"
            // 9 => "website"
            // 10 => "contact_first_name"
            // 11 => "contact_last_name"
            // 12 => "contact_email_address"

            try {
                $user = User::where('email', $row->contact_email_address)->first();
                if (!$user) {
                    $user->referral_user_id = $this->user()->id;
                    $user->first_name = $row->contact_first_name;
                    $user->last_name = $row->contact_last_name;
                    $user->email = $row->contact_email;
                    $user->password = Hash::make(str_random(30));
                    $user->company_name = $row->business_name;
                    $user->address = $row->street_address;
                    $user->secondary_address = $row->secondary_address;
                    $user->city = $row->city;
                    $user->state = $row->state;
                    $user->zip = $row->zip;
                    $user->country = $row->country;
                    $user->phone = $row->phone;
                    $user->website = $row->website;
                    $user->ip = $request->ip();
                    $user->save();
                }

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/textsearch/json?key='.env('GOOGLE_PLACES_API_KEY').
                    '&query='.urlencode(
                        $row->business_name.
                        ' '.$row->street_address.
                        ' '.$row->secondary_address.
                        ' '.$row->city.
                        ' '.$row->state.
                        ' '.$row->zip.
                        ' '.$row->country
                    )
                );
                $details = json_decode($response->getBody());

                if (!isset($details->results[0])) {
                    throw new \Exception("No results"); 
                }

                $location = new Location;
                $location->title = $row->location_name_optional;
                $location->address = $row->street_address;
                $location->secondary_address = $row->secondary_address;
                $location->city = $row->city;
                $location->state = $row->state;
                $location->zip = $row->zip;
                $location->country = $row->country;
                $location->phone = $row->phone;
                $location->text_phone = env('TWILIO_TEXT_PHONE');
                $location->text_phone_id = env('TWILIO_TEXT_PHONE_ID');
                $location->website = $row->website;
                $location->review_invite_text_content = $user->default_invite_content;
                $location->review_invite_email_content = $user->default_invite_content;
                $location->billing_interval = '+1 Month';
                $location->billing_amount = 50;
                $location->billing_start_at = Carbon::now()->addMonths(1);
                $location->billing_next_bill_at = Carbon::now()->addMonths(1);
                $location->save();


                $locationuser = new Locationuser;
                $locationuser->location_id = $location->id;
                $locationuser->user_id = $user->id;
                $locationuser->permission_type = 'trial';
                $locationuser->save();

                $googleplace = new Googleplace;
                $googleplace->location_id = $location->id;
                $googleplace->is_invite = 1;
                $googleplace->title = $details->results[0]->name;
                $googleplace->place_id = $details->results[0]->id;
                $googleplace->rating = $details->results[0]->rating;
                $googleplace->save();

                $count++;
            }
            catch (\Exception $e) {
                $skipped[$index] = $row;
            }
            
        }

        return $this->response->array([
            'message' => 'Imported Successfully',
            'status_code' => 200,
            'count' => $count,
            'skipped' => $skipped
        ]);
    }


}

