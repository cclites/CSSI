<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\User;
use App\Models\Transaction;
use App\Models\Type;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\Company;
use App\Models\Price;


// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Controller;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use DB;
use Cache;

use Carbon\Carbon;

class CompanyReportController extends Controller{
	
	
	/*
	 * Function generates check totals for a given company. These are loaded after
	 * the view is loaded, and rendered dynamically client side.
	 */
	public function show(Request $request){
		
		$data = [];
		$types = Type::all();

		$checks = Check::whereDate('completed_at', DB::raw('CURDATE()'))
		          ->where("company_id", $request->company_id)
				  ->where("sandbox", false)
				  ->get();
				  
	    			  
		//** CHECKS FOR DAY  **//	  
		$data["checks_count_for_day"] = $checks->count();
		$data["types_for_day"] = [];
		$day["day_count"] = [];
		
		if($checks->count() > 0){
			
			foreach($checks as $check){
	
				foreach($check->checktypes as $type){

					if(!isset($data["types_for_day"][$types[$type->type_id-1]->title])){
						$data["types_for_day"][$types[$type->type_id-1]->title] = [
						  "count" => 1,
						  "amount" => $type->price(),
						  "type_id" => $type->type_id,
						];
					}else{
						$data["types_for_day"][$types[$type->type_id-1]->title]["count"] += 1;
						$data["types_for_day"][$types[$type->type_id-1]->title]["amount"] += $type->price();
					}
					
					
					if( !isset($data["day_count"][$type->created_at]) ){
						$data["day_count"][$type->created_at] = 1;
					}else{
						$data["day_count"][$type->created_at] += 1;
					}
			
				}
			}
		}
		
		//** CHECKS FOR MONTH  **//
		
		$checks = Check::whereDate('completed_at', '>=', Carbon::now()->startOfMonth()->startOfDay())
		          ->where("company_id", $request->company_id)
				  ->where("sandbox", false)
				  ->get();
						
		$data["checks_count_for_month"] = $checks->count();
		$data["types_for_month"] = [];
		$data["month_count_per_day"] = [];
		
		if($checks->count() > 0){
			
			foreach($checks as $check){
				
				foreach($check->checktypes as $type){
					
					if(!isset($data["types_for_month"][$types[$type->type_id-1]->title])){
						$data["types_for_month"][$types[$type->type_id-1]->title] = [
						  "count" => 1,
						  "amount" => $type->price(),
						  "type_id" => $type->type_id,
						];
					}else{
						$data["types_for_month"][$types[$type->type_id-1]->title]["count"] += 1;
						$data["types_for_month"][$types[$type->type_id-1]->title]["amount"] += $type->price();
					}
					
					
					$createdAt = substr($type->created_at, 0, 10);

					
					if( !isset($data["month_count_per_day"][$createdAt]) ){
						$data["month_count_per_day"][$createdAt] = 1;
					}else{
						$data["month_count_per_day"][$createdAt] += 1;
					}
	
				}
			}
		}
		
		//** CHECKS FOR PREVIOUS MONTH  **//
		$checks = Check::whereBetween('completed_at', [
							Carbon::today()->startOfMonth()->subMonth()->startOfDay(),
							Carbon::today()->subMonth()->endOfMonth()->endOfDay()
						])
						->where("company_id", $request->company_id)
						->where("sandbox", false)
						->get();
									
		$data["checks_count_for_previous_month"] = $checks->count();
		$data["types_for_previous_month"] = [];
		$data["previous_month_count_per_day"] = [];
		
		if($checks->count() > 0){
			
			foreach($checks as $check){

				foreach($check->checktypes as $type){
						
					if(!isset($data["types_for_previous_month"][$types[$type->type_id-1]->title])){
						$data["types_for_previous_month"][$types[$type->type_id-1]->title] = [
						  "count" => 1,
						  "amount" => $type->price(),
						  "type_id" => $type->type_id,
						];
					}else{
						$data["types_for_previous_month"][$types[$type->type_id-1]->title]["count"] += 1;
						$data["types_for_previous_month"][$types[$type->type_id-1]->title]["amount"] += $type->price();
					}
					
					$createdAt = substr($type->created_at, 0, 10);
					
					if(!isset($data["previous_month_count_per_day"][$createdAt])){
						$data["previous_month_count_per_day"][$createdAt] = 1;
					}else{
						$data["previous_month_count_per_day"][$createdAt] += 1;
					}
	
				}
			}
		}
		
		//** CHECKS YTD **//
		$checks = Check::whereBetween('completed_at', [
						Carbon::now()->startOfYear(),
						Carbon::now()
					])
					->where("company_id", $request->company_id)
					->where("sandbox", false)
					->get();
					
		$data["checks_count_for_ytd"] = $checks->count();
		$data["types_for_ytd"] = [];
		$data["ytd_count_per_day"] = [];
		
		if($checks->count() > 0){
			
			foreach($checks as $check){

				foreach($check->checktypes as $type){

					if(!isset($data["types_for_ytd"][$types[$type->type_id-1]->title])){
						$data["types_for_ytd"][$types[$type->type_id-1]->title] = [
						  "count" => 1,
						  "amount" => $type->price(),
						  "type_id" => $type->type_id,
						];
					}else{
						$data["types_for_ytd"][$types[$type->type_id-1]->title]["count"] += 1;
						$data["types_for_ytd"][$types[$type->type_id-1]->title]["amount"] += $type->price();
					}
					
					$createdAt = substr($type->created_at, 0, 10);
					
					if(!isset($data["ytd_count_per_day"][$createdAt])){
						$data["ytd_count_per_day"][$createdAt] = 1;
					}else{
						$data["ytd_count_per_day"][$createdAt] += 1;
					}

				}
			}
		}

		return json_encode($data);
	}

	/*
	 * Special report for a reseller, allowing them to view compaies they have sold to.
	 * This was a one-off request.
	 */
    public function limitedAdminCompanyReport(Request $request){
    	
		//return json_encode( ["message"=>"Hit the V1 controller"]);
		$user_id = $request->user_id;
		
		$viewable = DB::table("viewable_companies")
					->where('user_id', $user_id)
					->pluck('company_id')->toArray();

		$previous = false;
		
		//Admins get the report for the previous month. Would have been easier to just 
		//check for an admin role instead. 
		if(isset($request->previous)){
			$previous = true;
		}
	
		$companies = Company::companies()->whereIn('company_id', $viewable);	 
		$types = Cache::get('types');
			 
		$report = "MONTH,COMPANY,QTY,ITEM,AMOUNT\n";
					 
		foreach($companies as $company){

			$transactions = collect([]);
  
			foreach($company->members() as $member){
				
				if($previous){
					$transactions = $transactions->merge($member->transactions);
				}else{
					$transactions = $transactions->merge($member->currentTransactions);
				}
				
			}
			
			
			$date = \Carbon\Carbon::now();
			
			if($previous){
				$date = $date->startOfMonth()->subMonth()->format("Y - m");
			}else{
				$date = $date->startOfMonth()->format("Y - m");
			}
			
			$cssiData["date"] = $date;
			
			//Would have been smarter to initialize programmatically.
			for($i = 0; $i<$types->count(); $i += 1){
				$cssiData["$i"]["count"] = 0;
                $cssiData["$i"]["amount"] = 0;
			}
			
			
			$cssiData["1"]["count"] = 0;
            $cssiData["1"]["amount"] = 0;
            $cssiData["2"]["count"] = 0;
            $cssiData["2"]["amount"] = 0;
            $cssiData["3"]["count"] = 0;
            $cssiData["3"]["amount"] = 0;
            
            $cssiData["4"]["count"] = 0;
            $cssiData["4"]["amount"] = 0;
            $cssiData["5"]["count"] = 0;
            $cssiData["5"]["amount"] = 0;
            $cssiData["6"]["count"] = 0;
            $cssiData["6"]["amount"] = 0;
            
            $cssiData["7"]["count"] = 0;
            $cssiData["7"]["amount"] = 0;
            $cssiData["8"]["count"] = 0;
            $cssiData["8"]["amount"] = 0;
            $cssiData["9"]["count"] = 0;
            $cssiData["9"]["amount"] = 0;
            
            $cssiData["10"]["count"] = 0;
            $cssiData["10"]["amount"] = 0;
			
			
	        $cssiData["11"]["count"] = 0;
	        $cssiData["11"]["amount"] = 0;
	        $cssiData["12"]["count"] = 0;
	        $cssiData["12"]["amount"] = 0;
	        $cssiData["13"]["count"] = 0;
	        $cssiData["13"]["amount"] = 0;
			
			$cssiData["14"]["count"] = 0;
	        $cssiData["14"]["amount"] = 0;
	    
	        foreach($transactions as $transaction){
	        	
				$type_tuples = explode(",", $transaction->check_type);
				
				foreach($type_tuples as $type){
						
					if($type == ""){
						continue;
					}
					
					//Log::info(gettype($type));
					//Log::info(">" .$type . "<");
					//$cssiData[$transaction->check_type]["count"] += 1;
		        	//$cssiData[$transaction->check_type]["amount"] += $transaction->amount;
						
					$cssiData[$type]["count"] += 1;
					
					//Need to look up the price
					$comp_id = $transaction->parent_id;
					//Log::info("Company is is " . $comp_id);
					
					$owner_id = User::where('company_id', $comp_id)
					            ->where('company_rep', true)
								->pluck('id');
						
					//Log::info("Owner id is " . $owner_id[0]);		
					//Log::info("Type here is " . $type);
							 
					$price = Price::where("user_id", $owner_id[0])
					          ->where('type_id', (int)$type)
							  ->first();
							  
					//Log::info("Amount is " . $price->amount);
					
		        	$cssiData[$type]["amount"] += floatVal($price->amount);
				}
				
	        				
	        }
			
			for( $i=1; $i<count($types) + 1; $i += 1){
					
				if($cssiData[$i]["count"] > 0){
					//$report .= $date . "," . $company->data->company_name . "," . $cssiData[$i]["count"] . "," . $types[$i-1]->title . ",$" . number_format($cssiData[$i]["amount"], 2) . "\n";
					
					$report .= $date . "," . $company->data->company_name . "," . $cssiData[$i]["count"] . "," . $types[$i-1]->title . ",$" . number_format($cssiData[$i]["amount"], 2, '.', '') . "\n";
				}	
			}
	 
		}

		return json_encode($report);

    }
	
}

?>