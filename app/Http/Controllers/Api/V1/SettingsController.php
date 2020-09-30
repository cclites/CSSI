<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\User;
use App\Models\Transaction;
use App\Models\Configuration;

// Transformers
use \App\Transformers\Api\V1\UserTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Stripe
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

use Log;
use DB;
use Hash;
use Storage;
use Artisan;
use Image;
use File;
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class SettingsController extends Controller
{
	//Update billing info
    public function billing(Request $request)
    {
    	
        $validator = Validator::make($request->all(), [
            'stripeToken' => 'required',
        ]);

        if ($validator->fails()) {
            throw new UpdateResourceFailedException('Could not update your info.', $validator->errors());
        }

        if ($this->user()->stripe_customer_id) {
            $stripeCustomer = Customer::retrieve([
                'id' => $this->user()->stripe_customer_id,
                'expand' => ['default_source']
                ]);
            $stripeCustomer->source = $request->stripeToken;
            $stripeCustomer->save();
        }
        else {
            $stripeCustomer = Customer::create([
                'source'   => $request->stripeToken,
                'email'    => $this->user()->email,
                'description'    => $this->user()->full_name,
                ]);
				
			Log::info("Adding a stripe customer");
        }
		
		if(isset($this->user()->apiuser)){
			unset($this->user()->apiuser);
		}

        $this->user()->stripe_customer_id = $stripeCustomer->id;
        $this->user()->card_brand = $stripeCustomer->sources->data[0]->brand;
        $this->user()->card_last_four = $stripeCustomer->sources->data[0]->last4;
        $this->user()->card_expiration = $stripeCustomer->sources->data[0]->exp_month.'/'.$stripeCustomer->sources->data[0]->exp_year;
        $this->user()->save();
		
		$users = User::where('company_id', $this->user()->company_id)->get();
		
		foreach($users as $user){
			$user->stripe_customer_id = $stripeCustomer->id;
       		$user->card_brand = $stripeCustomer->sources->data[0]->brand;
        	$user->card_last_four = $stripeCustomer->sources->data[0]->last4;
            $user->card_expiration = $stripeCustomer->sources->data[0]->exp_month.'/'.$stripeCustomer->sources->data[0]->exp_year;
            $user->save();
		}

        return $this->response->array([
            'message' => 'Your credit card information has been saved.',
            'status_code' => 200
        ]);
        
    }

    //Function called by Mobile App API (and probably won't be used)
    public function appBilling(Request $request){
    	
		Log::info("******* appBilling *******");
		$s = print_r($request->all(), true);
		Log::info($s);
    	
		if(isset($this->user()->apiuser)){
			unset($this->user()->apiuser);
		}

        $this->user()->stripe_customer_id = $request->stripe_customer_id;
        $this->user()->card_brand = $request->card_brand;
        $this->user()->card_last_four = $request->card_last_four;
        $this->user()->card_expiration = $request->card_expiration;
        $this->user()->save();
		
		return $this->response->array([
            'message' => 'Your credit card information has been saved.',
            'status_code' => 200
        ]);
		
		//How do we want to handle the check data?
		
	}

    //update contact info
    public function contact(Request $request) {
    	
		Log::info("SettingsController::contact");

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|max:191',
            'phone' => 'required|min:10|max:50',
            'email' => 'required|email|unique:users,email,'.$this->user()->id.'|max:191',
            'website' => 'nullable|max:191',

            'address' => 'required|max:191',
            'secondary_address' => 'nullable|max:191',
            'city' => 'required|max:191',
            'state' => 'required|min:2|max:191',
            'zip' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            throw new UpdateResourceFailedException('Could not update your info.', $validator->errors());
        }
		
		//didn't need to set this anyway. Could have checked for role instead.
		if(isset($this->user()->apiuser)){
			unset($this->user()->apiuser);
		}
		
		$companyId = $this->user()->company_id;
		
		//Log::info($companyId);
		
		$company = \App\_Models\Company::where('company_id', $companyId)->first();
		
		if(!$company){
			$company = new \App\_Models\Company();
		}
		//Log::info("showing company??");
		//Log::info(json_encode($company));
		
		
		//$company = new \App\_Models\Company();
        $this->user()->is_setup_contact = true;
		
        $this->user()->company_name = $request->company_name;
        $this->user()->phone = databasePhone($request->phone);
        $this->user()->email = $request->email;
        $this->user()->website = $request->website;
        $this->user()->address = $request->address;
        $this->user()->secondary_address = $request->secondary_address;
        $this->user()->city = $request->city;
        $this->user()->state = $request->state;
        $this->user()->zip = $request->zip;
		
		try{
			$company->company_name = $request->company_name;
	        $company->phone = databasePhone($request->phone);
	        $company->email = $request->email;
	        $company->website = $request->website;
	        $company->address = $request->address;
	        $company->secondary_address = $request->secondary_address;
	        $company->city = $request->city;
	        $company->state = $request->state;
	        $company->zip = $request->zip;
		}catch(\Exception $e){
			Log::info("Unable to add to _company");
		}
		

		if(Auth::check() && isset($request->companyId)){
			$this->user()->company_id = $request->companyId;
			
			try{
				$company->company_id = $request->companyId;
			}catch(\Exeption $e){
				Log::info("Unable to add to _company v1");
			}
		}
		
		$this->user()->invoice = $request->invoice;
			
			
			
		if( preg_match('/\\d/', $request->cell_phone) ){
			$this->user()->cell_phone = $request->cell_phone;
		}else{
			$this->user()->cell_phone = "";
		}
		
		
		$this->user()->extension = $request->extension;
		
		try{
			$company->invoice = $request->invoice;
			$company->cell_phone = $request->cell_phone;
			$company->extension = $request->extension;
			$company->save();
		}catch(\Exception $e){
			Log::info(json_encode($e));
			Log::info("Unable to add to _company v1");
		}
	
        $this->user()->save();

		$users = User::where('company_id', $this->user()->company_id)->where('id', '!=', $this->user()->id)->get();
		
		//if the user is a company owner, any updates to company contact info propogates
		//to employees.
		foreach($users as $user){
			$user->is_setup_contact = 1;
	        $user->company_name = $request->company_name;
	        $user->website = $request->website;
	        $user->address = $request->address;
	        $user->secondary_address = $request->secondary_address;
	        $user->city = $request->city;
	        $user->state = $request->state;
	        $user->zip = $request->zip;
			$user->company_id = $request->companyId;
			$user->invoice = $request->invoice;
	        $user->save();
		}
		

        return $this->response->array([
            'message' => 'Your contact information has been updated.',
            'status_code' => 200
        ]);
    }

    //update password
    public function password(Request $request) {

        $validator = Validator::make($request->all(), [
            'current_password' => 'max:191',
            'password' => 'min:5|max:191',
            'password_confirmation' => 'same:password',
        ]);


        // Chad Clites - chad@extant.digital
        // 10/29/2018
        // Bypassed this check because it is worthless to people that have
        // lost their password since they cannot get to the password reset page,
        // and there was no way for the admin to create a new password for the user
        
        /*
        $validator->after(function ($validator) use ($request) {
        				
			if(!Hash::check($request->current_password, auth()->user()->password)){
				$validator->errors()->add('current_password', 'Your current password doesn\'t match the password you submitted. If you have forgotten your password, logout and then click on the "forgot password" link.');
			}
			
        });
		 * 
		 */
 
        if ($validator->fails()) {
            throw new UpdateResourceFailedException('Could not change your password.', $validator->errors());
        }

        auth()->user()->password = Hash::make($request->password);
        auth()->user()->save();

        return $this->response->array([
            'message' => 'Your password has been updated.',
            'status_code' => 200
        ]);
    }


	public function sandboxEnable(Request $request){
		
	  	$this->user()->sandbox = true;
	  	$this->user()->save();
	  
	  	return json_encode(["success"=>1]);
	}
	
	public function sandboxDisable(Request $request){
		$this->user()->sandbox = false;
	  	$this->user()->save();
		
		return json_encode(["success"=>1]);
	}
	
	//updates the site configs
	public function configs(Request $request){
		
		if ($request->isMethod('post')) {
			
			$request["LAST_UPDATED"] = time();
			$configs = \App\Models\Configuration::all();
			
			foreach($configs as $config){
							
				$name = $config->name;
				
				if(isset($request[$name])){
					$config->value = $request[$name];
					$config->save();	
				}
			
			}
			
			updateConfigs();

			return back();
			//return view('admin/configs/index');
			
        }

        if ($request->isMethod('get')) {
    		return view('admin/configs/index');
        }

	}
	
	public function achRegister(Request $request){
		
		Log::info("Api/V1/SettingsController::achRegister");
		$headers[] = 'Content-Type: application/json';
		
		$params = array(
		   'client_id' => env('PLAID_ACH_CLIENT'),
		   'secret' => env('PLAID_ACH_SECRET'),
		   'public_token' => $request->plaid_token,
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://sandbox.plaid.com/item/public_token/exchange");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		if(!$result = curl_exec($ch)) {
		   trigger_error(curl_error($ch));
		}
		curl_close($ch);
		
		Log::info("Retrieved first result: " . $result);
		
		$jsonParsed = json_decode($result);
		
		$btok_params = array(
		   'client_id' => env('PLAID_ACH_CLIENT'),
		   'secret' => env('PLAID_ACH_SECRET'),
		   'access_token' => $jsonParsed->access_token,
		   'account_id' => $request->account_id,
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://sandbox.plaid.com/processor/stripe/bank_account_token/create");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($btok_params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		if(!$result = curl_exec($ch)) {
		   trigger_error(curl_error($ch));
		}
		curl_close($ch);

		$result = json_decode($result);		
		$user = $this->user();
		$user->stripe_customer_id = $result->stripe_bank_account_token;
		$user->save();
		return json_encode(["result"=>1]);
		
		
		
	}

    //registers a user as a limited admin
	public function limitedAdmin(Request $request){
		
		$user_id = $request->user_id;
		
		if(isset($request->viewable_companies)){

			$viewable = $request->viewable_companies;
			
			DB::table("viewable_companies")->where('user_id', $user_id)->delete();
			
			foreach($viewable as $company){
				DB::table("viewable_companies")->insert(['user_id'=>$user_id, 'company_id'=>$company]);
			}
			
		}

        return json_encode(['status'=>1, 'message'=>'Updated Limited Admin Settings']);
		
	}
	
	public function removePaymentInfo(Request $request){
		
		$user = User::find($request->user_id);
		$user->card_brand = null;
		$user->stripe_customer_id = null;
		$user->card_last_four = null;
		$user->card_expiration = null;
		$user->save();
		
		return json_encode(["message"=>'Payment information removed']);
		
	}

}