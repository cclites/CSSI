<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;

use App\_Models\Company;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Auth;
use Parser;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use DB;

use View;

class CompanyController extends Controller{
	
	public function index(Request $request){
		
		Log::info("Admin/CompanyController::index");
		
		$companies = [];
		
		if(auth()->user()){
			$companies = Company::all();
		}

		$companies = $this->api
            		 ->be(auth()->user())
            		 ->get('api/admin/companies');
					 
		//return json_encode($companies);
		return View::make('admin.companies.index', ['companies'=>$companies]);

	}
	
	//Used for B to B checks
	public function screen(Request $request){
		
		Log::info("App\Http\Controllers::screen");
		
		try {
            $status = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/company/screen');
        } catch (ResourceException $e) {
        	Log::info("Catch error");
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }
		
		flash('Your email has been sent')->important();
		
		return back();	
	}
	
	//Used to validate link in B to B checks.
	public function viewScreen(Request $request){
		
		Log::info("App\Http\Controllers::viewScreen");

		try {
	
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				Log::info("Unable to authenticate");
				return response()->json(['Unable to authenticate token'], 400);
			}
	
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			return response()->json(['token_expired'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['token_invalid'], $e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			return response()->json(['token_absent'], $e->getStatusCode());
		}

		if($request["key"] !== Auth::user()->key) {
			return response()->json(['key_not_valid'], 400);
		}

		try {
            $params = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/company/screen/view');
			
			$companyName = Auth::user()->company_name;
			
			//Since the token in the email returns an authorized user, and we don't want
			//employees getting into places they are not supposed to, the user must be
			//logged out once the view is rendered
			Auth::logout();
			
        } catch (ResourceException $e) {
        	Log::info("**************  Catch error ***************");
			
			Auth::logout();
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }
		
		return view('whitelabel/btob')
            ->with('params', $params)
			->with('token', $request->token)
			->with('key', $request->key)
			->with('company_name', $companyName)
			->with('id', $request->id);

	}

    // Poorly named function. When the employee clicks on the link in the email,
    // We start by seeing if the request is active.
    // Used for B to B
	public function start(Request $request){
		
		$data = DB::table("b_to_b")
		        ->where("check_request_id", $request->id)
		        ->where('active', true)
		        ->first();
				
		if($data){
			
			$params = "?token=" . $data->token . "&key=" . $data->company_key . "&id=" . $request->id;		
		    $url = secure_url('company/screen/view/' . $params);
			
			return redirect($url);

		}else{
			abort(500, 'Resource not found.', []);
		}

	}
	
	public function approve($id)
    {
    	
		Log::info("Admin/CompanyController::approve");
		
        $user = $this->api
            ->be(auth()->user())
            ->get('api/company/users/'.$id.'/approve');

        flash('Account has been approved.')->important();
        return back();
    }

    public function disapprove($id)
    {
    	Log::info("Admin/CompanyController::disapprove");
		
        $user = $this->api
            ->be(auth()->user())
            ->get('api/company/users/'.$id.'/disapprove');
			
		flash("Account has been disapproved.")->warning();
        return back();
    }
	
	public function _update(Request $request){
		
		Log::info("Admin/CompanyController::_update");
		
		return $this->api
               ->be(auth()->user())
			   ->with($request->all())
               ->put('api/admin/company/_update/');

	}
	
	public function _updatePrices(Request $request){
		
		Log::info("Admin/CompanyController::_updatePrices");
		
		return $this->api
               ->be(auth()->user())
			   ->with($request->all())
               ->put('api/admin/company/prices/_update/');
			
	}
	
	public function _totals(Request $request){
		
		Log::info("Admin/CompanyController::_totals");
		
		return $this->api
               ->be(auth()->user())
			   ->with($request->all())
               ->put('api/admin/company/_totals');
		
	}
	
	
}