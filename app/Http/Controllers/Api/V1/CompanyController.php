<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

// Models
use App\Models\User;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Recipients\EmployeeRecipient;
use App\Notifications\EmployeeScreenEmail;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Parser;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use DB;


class CompanyController extends Controller{
	
	
	public function index(Request $request){
		
		Log::info("V1/CompanyController::index");
		
		$companies = \App\_Models\Company::orderBy('company_name', 'ASC')->get();
		
		//$employees = 
		
		//return \App\_Models\Company::with('prices')->get();
		//return \App\_Models\Company::all();
		//$companies = DB::table("cssi._companies")->get();
		
		foreach($companies as $company){

			$company['prices'] = \App\_Models\Price::where('company_id', $company->company_id)->get();
			$company["employees"] = DB::table("cssi.users")->where('company_id', $company->company_id)->orderBy('company_rep', 'DESC')->get();
			
			//Log::info(json_encode($company));
		}
		
		return $companies;
		
	}
	
	
	/*
	 * Used for B to B checks. Creates a record for each employee, and sends them
	 * an email with a link to a whitelabeled form.
	 */
	public function screen(Request $request){

	   $emails = explode(",", $request->email);
	   
	   foreach($emails as $email){
	   	
		   $email = trim($email);
	   	
		   //need this in the url 
		   $checkRequestId = createSeed(16);
		   $companyId = $this->user()->company_id;
		   $token = JWTAuth::fromUser($this->user());
		   $key = $this->user()->key;
		   
		   DB::table("b_to_b")->insert([
			 "company_id"=>$companyId,
			 "check_request_id"=>$checkRequestId,
			 "request" => json_encode($request->all()),
			 "email" => $email,
			 "token" => $token,
			 "company_key" => $key
		   ]);
		   
		   $recipient = new EmployeeRecipient($email);
		   $recipient->notify(new EmployeeScreenEmail($checkRequestId, $this->user()->company_name, $this->user()->email));

	   }
	   
		   

	   return 0;
		
	}
	
	/*
	 * Used for B to B checks. Verifies that the employee has not already entered in their information,
	 * and that a record exists for the check, and is active.
	 */
	public function viewScreen(Request $request){
		
		Log::info("V1/CompanyController::viewScreen");

		$checkParams = DB::table("b_to_b")
		               ->where('check_request_id', $request->id)
					   ->first();
		 			   
		if(!$checkParams){
			return response()->json(['The request is invalid'], 400);
		}elseif(!$checkParams->active){
			return response()->json(['This token has already been used'], 400);
		}else{
		   return $checkParams->request;
		   
		}
	}
	
	public function approve($id) {
    	
		Log::info("V1/CompanyController::approve");
		
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->is_approved = 1;
        $user->save();
		
		return back();

        //return $this->response->item($user, new UserTransformer);
    }

    public function disapprove($id) {
    	
		Log::info("V1/CompanyController::approve");
		
        $user = User::where('id', $id)
                ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->is_approved = 0;
        $user->save();

        return back();
    }
	
	public function _update(Request $request){
    
	    $company = \App\_Models\Company::where('company_id', $request->id)->first();
		$company[$request->type] = $request->val;
		$company->save();

		return 0;		
		//{"id":"XjosPE","val":"222","type":"extension","_token":"mSQmJoAJM27ej2AnvRu8Wc74V764XUrHsftSlA2u"}

	}
	
	
	public function _updatePrices(Request $request){
		
		//{"id":"eeeeee","val":"0.02","type":"1","_token":"mSQmJoAJM27ej2AnvRu8Wc74V764XUrHsftSlA2u"}
		
		$price = \App\_Models\Price::where('type_id', $request->type)->where('company_id', $request->id)->first();
		$price->amount = $request->val;
		$price->save();
        return 0;
	}
	
	
	public function _totals(Request $request){
			
		//$request = {"company_id":"ZGY6f1","_token":"mSQmJoAJM27ej2AnvRu8Wc74V764XUrHsftSlA2u"}
				
		$totals = [];
		$companyId = $request->company_id;
		
		$totals["check_for_days"] = $this->checksForDay($companyId);
		$totals["checks_for_month"] = $this->checksForMonth($companyId);
		$totals["checks_prior_month"] = $this->checksForPriorMonth($companyId);
		$totals["checks_ytd"] = $this->checksForYtd($companyId);
		$totals["company_id"] = $companyId;
		
		
		
        return json_encode($totals);
	}
	
	public function checksForDay($companyId){
		
		Log::info("Api/V1/Admin/ReportController:checksForDay");
		$today = \Carbon\Carbon::today()->startOfDay();
		$today->setTimezone('UTC');
		
		$orders = DB::table('cssi._orders')
		          ->where('completed_at', '>=', $today)
				  ->where('company_id', $companyId)
		          ->get();
				  
		$checksArray = [];
		
		foreach($orders as $order){
			
			$checks = \App\_Models\Check::where('order_id', $order->id)->get();
			
			foreach($checks as $check){
				
				$checksArray[] = $check;
			}
		}

		return $this->calculateTotals($checksArray);

	}
	
	
	
	public function checksForMonth($companyId){
		 
		Log::info("Api/V1/Admin/ReportController:checksForMonth");
		
		$start = \Carbon\Carbon::today()->startOfDay()->startOfMonth();
		$start->setTimezone('UTC');
		
		
		$orders = DB::table('cssi._orders')
		          ->where('completed_at', '>=', $start)
				  ->where('company_id', $companyId)
		          ->get();
				  
		$checksArray = [];
		
		foreach($orders as $order){
			
			$checks = \App\_Models\Check::where('order_id', $order->id)->get();
			
			foreach($checks as $check){
				
				$checksArray[] = $check;
			}
		}

		return $this->calculateTotals($checksArray);
		
	}
	
	public function checksForPriorMonth($companyId){

		Log::info("Api/V1/Admin/ReportController:checksForPriorMonth");
		
		$start = \Carbon\Carbon::today()->startOfDay()->startOfMonth()->subMonth();
		$start->setTimezone('UTC');
		
		$end = \Carbon\Carbon::today()->startOfDay()->subMonth()->endOfMonth();
		$end->setTimezone('UTC');
		
		$orders = DB::table('cssi._orders')
		          ->where('completed_at', '>=', $start)
		          ->where('completed_at', "<=", $end)
				  ->where('company_id', $companyId)
		          ->get();
				  
		$checksArray = [];
		
		foreach($orders as $order){
			
			$checks = \App\_Models\Check::where('order_id', $order->id)->get();
			
			foreach($checks as $check){
				
				$checksArray[] = $check;
			}
		}
		

		return $this->calculateTotals($checksArray);
	}
	
	public function checksForYtd($companyId){
		
		Log::info("Api/V1/Admin/ReportController:checksForYtd");
		
		$start = \Carbon\Carbon::today()->startOfYear()->startOfDay();
	    $start->setTimezone('UTC');
	    
	    $orders = DB::table('cssi._orders')
		          ->where('completed_at', '>=', $start)
				  ->where('company_id', $companyId)
		          ->get();
				  
		$checksArray = [];
		
		foreach($orders as $order){
			
			$checks = \App\_Models\Check::where('order_id', $order->id)->get();
			
			foreach($checks as $check){
				
				$checksArray[] = $check;
			}
		}

		return $this->calculateTotals($checksArray);
		
	}

    public function calculateTotals($checks){
    	
		$checkCounts = [];
		$cssiData = [11,12,13];
		$typesCount = cache('types')->count();
		$types = cache('types');
		$times = [];
		$cnt = 0;
		
		$totalAmount = 0;
		
		//$totalAmount = displayMoney($checks->sum('amount'));
		//$totalAverage = displayMoney($checks->average('amount'));
		
		
        //Initialize the array
		foreach($types as $type){
			
			if(!in_array($type->id, $cssiData)){
				$checkCounts[$type->id] = [ 'count'=>0, 'title'=>$type->title, 'amount'=>0, 'color' => $type->color];
			}
			                          
		}
		
		foreach($checks as $check){
			
			if(!in_array($check->type, $cssiData) && isset($checkCounts[$check->type]) ){
				
				$checkCounts[$check->type]["count"] += 1;
				$checkCounts[$check->type]["amount"] += $check->amount;
				$times[] = $check->created_at;
				$totalAmount += $check->amount;
				$cnt += 1;
				
			}
				
		}
		
		//$totalAverage = $totalAmount/$cnt;
		$totalAverage = 0;
		
		$data = [
		        	'counts' => $checkCounts,
		        	'totals' => [
		        	    'count' => $cnt,
		        	    'average' => $totalAverage,
		        	    'amount' => $totalAmount
 		        	],
 		        	'times' => $times
				];
		
		return $data;
    }
	
	
	
}