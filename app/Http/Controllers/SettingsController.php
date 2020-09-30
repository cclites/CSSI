<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Facebook\Facebook;

use Log;
use Auth;
use DB;
use App\Http\Requests;
use Illuminate\Http\Request;
use Response;

class SettingsController extends Controller
{
    public function index() {
        return view('settings/index');
    }

    public function account(Request $request)
    {
        return view('settings/account');
    }

    public function contact(Request $request)
    {
        if (!$request->all()) {
            return view('settings/contact');
        }

        try {
            $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/settings/contact');
        } catch (UpdateResourceFailedException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

        flash('Your contact information has been updated.')->important();
        return redirect(secure_url('settings'));
    }
	
	//Used for mobile app contact
	public function appContact(Request $request){
		
		if (!$request->all()) {
			
			$u = auth()->user();
            
			$contact = [
			  'company_name' => $u->company_name,
			  'phone' => $u->phone,
			  'extention' => $u->extension,
			  'cell_phone' => $u->cell_phone,
			  'email' => $u->email,
			  'company_id' => $u->company_id,
			  'address' => $u->address,
			  'secondary_address' => $u->secondary_address,
			  'city' => $u->city,
			  'state' => $u->state,
			  'zip' => $u->zip,
			  'company_rep' => $u->company_rep,
			  'invoice' => $u->invoice,
			];
			
			return Response::json(array(
						            'status' => 1,
						            'message' => "Contact Information",
									'contact' => $contact),
						            200
						          );
			
        }

        try {
            $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/settings/contact');
        } catch (UpdateResourceFailedException $e) {
        	
		   Log::info("CSSI App Contact update failure");
		   
           return Response::json(array(
						            'status' => 0,
						            'message' => "CSSI App Contact update failure.",
									'errors' => $e->getErrors()),
						            200
						          );
        }

        Log::info("CSSI App Contact update Success");
        return Response::json(array(
						            'status' => 1,
						            'message' => "CSSI App Contact update Success."),
						            200
						          );
	}

    public function password(Request $request)
    {
        if (!$request->all()) {
            return view('settings/password');
        }

        try {
            $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/settings/password');
        } catch (UpdateResourceFailedException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

        flash('Your password has been updated.')->important();
        return redirect(secure_url('settings'));
    }
    

    public function billing(Request $request)
    {
        if (!$request->all()) {
            return view('settings/billing');
        }

        try {
            $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/settings/billing');
        } catch (UpdateResourceFailedException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

        flash('Your billing information has been updated!')->important();
        return redirect(secure_url('settings') );
    }
	
	//Will be used for mobile app
	public function appBilling(Request $request){
		
		/*
		if (!$request->all()){
			
			//$u = auth()->user();
			
			$billing = [
			    'card_last_four' => $u->card_last_four,
			    'card_brand' => $u->card_brand,
			    'card_expiration' => $u->card_expiration
			];
			
			return Response::json(array(
						            'status' => 1,
						            'message' => "Billing Information",
									'billing' => $billing),
						            200
						          );
			 
		}
		 */
								  
		try {
            $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/settings/appBilling');
        } catch (UpdateResourceFailedException $e) {
            Log::info("CSSI App Billing update failure");
		   
           return Response::json(array(
						            'status' => 0,
						            'message' => "CSSI App Billing update failure.",
									'errors' => $e->getErrors()),
						            200
						          );
        }
		
		Log::info("CSSI App Billing update Success");
        return Response::json(array(
						            'status' => 1,
						            'message' => "CSSI App Billing update Success."),
						            200
						          );
			
		
	}

    public function viewApi(Request $request)
    {
        return view('settings/api');
    }

    public function awaitingApproval(Request $request)
    {
        return view('settings/awaiting_approval');
    }
	
	public function achRegister(Request $request){
		
		try {
           return $this->api
	            ->be(auth()->user())
	            ->with($request->all())
	            ->post('api/settings/ach');
        } catch (UpdateResourceFailedException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }
		
	}
	
	public function removePaymentInfo(Request $request){
		
		Log::info("In SetingsController/removePaymentInfo");
		
		try {
            return $this->api
            		->be(auth()->user())
            		->with($request->all())
            		->post('api/settings/removePaymentInfo');
        } catch (UpdateResourceFailedException $e) {
        	
			Log::info("Returning errors");
			
            return back()
            		->withErrors($e->getErrors())
            		->withInput($request->all());
        }
	}
	
	public function addTouAccept($id){
		
		//add the user, but then what?
		DB::table('tou_accept')
			->insert([
				'user_id'=>$id
			]);

		return redirect('reports');
		
	}
	
	
}