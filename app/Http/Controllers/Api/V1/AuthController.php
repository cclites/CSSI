<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\User;
use App\Models\Type;
use App\Models\Price;
use App\Models\Coupon;
use App\Models\Transaction;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

// Transformers
use \App\Transformers\Api\V1\UserTransformer;

// Notifications
use Notification;
use \App\Notifications\PasswordResetEmail;
use \App\Notifications\WelcomeEmail;
use \App\Notifications\ApproveNewUserEmail;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Hash;
use DB;
use Carbon\Carbon;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;

use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $messages = [];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            throw new ResourceException('Could not authenticate.', $validator->errors());
        }

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        //update the user table so we know when last logged in
        auth()->user()->touch();
		// all good so return the token
        return response()->json(compact('token'));
    }

    public function password(Request $request)
    {
        $messages = [];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], $messages);

        if ($validator->fails()) {
            throw new ResourceException('Could not send password reset link.', $validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        $user->password_reset = str_random(100);
        $user->save();
        
        $user->notify(new PasswordResetEmail);
        return $this->response->array([
            'message' => 'A link to reset your password has been sent to '.$request->email,
            'status_code' => 200
        ]);

    }

    public function reset(Request $request)
    {
        $messages = [];

        $validator = Validator::make($request->all(), [
            'token' => 'required|min:100|max:100',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:5|max:255|confirmed',
        ], $messages);

        if ($validator->fails()) {
            throw new ResourceException('Could not reset password.', $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if ($user->password_reset != $request->token) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $user->password_reset = str_random(100);
        $user->password = bcrypt($request->password);
        $user->save();
        
        return $this->response->array([
            'message' => 'Your password has been reset',
            'status_code' => 200
        ]);
    }

    public function signup(Request $request)
    {

        $messages = [
            //'terms.required' => 'You must agree to the terms in order to continue',
            'email.unique' => 'The email is already in use with an existing account'
        ];
		
		
		
		$rules = [
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:users|min:8|max:191',
            'password' => 'min:5|max:191',
            'password_confirmation' => 'same:password|min:5|max:191',
            'terms' => 'required',
        ];
        
		if( !isset($request->is_app) ){
			$messages['terms.required'] = 'You must agree to the terms in order to continue';
		}

        $validator = Validator::make($request->all(), $rules, $messages);

        
        if ($validator->fails()) {
            throw new StoreResourceFailedException('Could not setup new account.', $validator->errors());
        }
		
		$userCompany = null;
		
		if(isset($request->companyId)){
			$company_id = $request->companyId;
			
			Log::info("Retrieving user Company");
			$userCompany = User::where("company_id", $request->companyId)->where('company_rep', true)->first();
			
			Log::info(json_encode($userCompany));
			
			if(!$userCompany){
			  Log::info("Create seed for null company");
			  $company_id = createSeed(6);
			}
			
		}else{
			$company_id = createSeed(6);
		}

        $user = new User();
        $user->first_name = ucfirst(strtolower($request->first_name));
        $user->last_name = ucfirst(strtolower($request->last_name));
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->ip = $request->ip();
		$user->company_id = $company_id;
		$user->company_rep = true;
		$user->is_app = isset($request->is_app) ? $request->is_app : false;
		$user->device = isset($request->device) ? $request->device : null;
		$user->sandbox = 0;
		
		if( isset($request->is_app ) ){
			$user->is_approved = true;
			$user->whitelabel_id = 4; //this is the id of the cssi data whitelabel entry in the DB
									  //This should be in a config somewhere
		}

		if($userCompany){
			$user->company_name = $userCompany->company_name;
			$user->address = $userCompany->address;
			$user->secondary_address = $userCompany->secondary_address;
			$user->city = $userCompany->city;
			$user->state = $userCompany->state;
			$user->zip = $userCompany->zip;
			$user->country = $userCompany->country;
			$user->website = $userCompany->website;
			$user->stripe_customer_id = $userCompany->stripe_customer_id;
			$user->card_brand = $userCompany->card_brand;
			$user->card_last_four = $userCompany->card_last_four;
			$user->card_expiration = $userCompany->card_expiration;
			$user->sandbox = false;
			$user->company_rep = false;
		}else{
			Log::info("No userCompany");
		}

        $user->save();

        $user->key = $user->id.'-'.rand(10000,999999);
        $user->save();
		
		DB::table("tou_accept")->insert(["user_id"=>$user->id]);
		
		$companyOwnerId = null;
		
		if($userCompany){
			$companyOwnerId = $userCompany->id;
		}
		
		$this->addToPricesForUser($user->id, $companyOwnerId);
		
        $user->notify(new WelcomeEmail);
        Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new ApproveNewUserEmail($user));
		
        return $this->response->item($user, new UserTransformer);
    }

	function addToPricesForUser($id, $companyOwnerId){
		
		if($companyOwnerId){
			
			$prices = DB::table('prices')
					  ->where('user_id', $companyOwnerId)
					  ->get();
					  
			foreach($prices as $p){
				$price = new Price();
			    $price->user_id = $id;
				$price->type_id = $p->type_id;
				$price->amount = $p->amount;
				$price->save();
			}
			
		}else{
			
			$types = DB::table('types')->get();
			
			foreach($types as $type){
				
				$price = new Price();
				$price->user_id = $id;
				$price->type_id = $type->id;
				$price->amount = $type->default_price;
				$price->save();
	
			}
		}

	}
}