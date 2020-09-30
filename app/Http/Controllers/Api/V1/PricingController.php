<?php

namespace App\Http\Controllers\Api\V1;

use Log;
use DB;
use Auth;
use Carbon\Carbon;

use App\Models\State;
use App\Models\User;
use App\Models\County;
use App\Models\Minimum;
use App\Models\Adjustment;


use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/*
 * Used by admin to update prices. These are all called with AJAX
 */
class PricingController extends Controller{
		
	public function putStatePrice(Request $request){
		Log::info("Update state price");

		DB::table("states")
			->where("id", $request->sId)
		    ->update(["mvr_cost"=>$request->amount]);
			
		return 0;
	}
	
	public function putStateExtra(Request $request){
		Log::info("Update state extra");

		DB::table("states")
			->where("id", $request->sId)
		    ->update(["extra_cost"=>$request->amount]);
			
		return 0;
	}
	
	public function putUserPrice(Request $request){
		Log::info("Update user price");

		DB::table("prices")
			->where("user_id", $request->cId)
			->where("type_id", $request->pId)
		    ->update(["amount"=>$request->amount,
		              "updated_at" => Carbon::now()
		            ]);
			
		return 0;
	}
	
	public function putBasePrice(Request $request){
		Log::info("Update check base price");
		
		DB::table("types")
			->where("id", $request->tId)
		    ->update(["default_price"=>$request->amount]);
			
		return 0;
	}
	
	public function putCountyExtra(Request $request){
		Log::info("Update county extra");

		DB::table("counties")
			->where("id", $request->cId)
		    ->update(["extra_cost"=>$request->amount]);
		    
		return 0;
	}
	
	public function adjustment(Request $request){
		
		$user = User::find($request->cId);
		$company_id = $user->company_id;
		
		//Log::info($request->amount);
		
		//$adjustment = DB::table("adjustments")->where('company_id', $company_id)->first();
		//$adjustment = Adjustment::where('company_id', $company_id)->first();
		//$adjustment = $user->adjustment;
		
		//Log::info(json_encode($adjustment));
		
		//if( $request->amount == 0 && !is_null($adjustment) ){
			//$adjustment->delete();
			//return 0;
		//}
		

		//if(is_null($adjustment)){
			
			//Log::info("Adjustment record does not exist");

			$adjustment = new Adjustment();
			$adjustment->company_id = $company_id;
			$adjustment->amount = $request->amount;
			$adjustment->initial_amount = $request->amount;
			$adjustment->added_by = Auth::user()->id;
			$adjustment->notes = "Adjustment added.";
			$adjustment->save();
			
		//}else{
			
			//Log::info("Update adjustment records");
			
			//$adjustment->notes .= "\n" . "Updated by : " . $user->getFullNameAttribute() ;
			//$adjustment->amount = $request->amount;
			//$adjustment->save();
		//}


        $adjustment = Adjustment::where('company_id', $company_id)->get();

		$data = [
		  'html' => "",
		  'amount' => 0
		];
        
		if($adjustment){
	
			foreach($adjustment as $adj){
				$data["html"] .= "<tr><td>" . $adj->created_at. "</td><td>" . $adj->amount . "</td></tr>";
			}
			
			$data["amount"] = $adjustment->sum("amount");
		}
		
		return json_encode($data);
	}
	
	public function minimum(Request $request){
		
		$user = User::find($request->cId);
		$company_id = $user->company_id;
		
		$min = DB::table("minimums")->where('company_id', $company_id)->first();
		
		if(!$min){
			Log::info("Minimums record does not exist");
			DB::table("minimums")->insert(['company_id'=>$company_id, 'amount'=> $request->amount]);
			
		}else{
				
			Log::info("Update Minimums records");
			DB::table("minimums")->where('company_id', $company_id)->update(['amount'=> $request->amount]);
		}
	
	}
	
}
	