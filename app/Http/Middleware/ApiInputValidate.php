<?php

namespace App\Http\Middleware;

// Models
//use App\Models\Log;
use Log;
use Auth;
use Route;
use JWTAuth;
use Closure;

use App\Models\Type;

class ApiInputValidate{
	
	//This translates the named version of the check into the proper
	//numerical code. All eyeforsecurity API checks run through here.
	public function handle($request, Closure $next){
		
		Log::info("Api Input Validate");
		
		//If we are retrieving a record, there is no need to do anything here.
		if (strpos($request->path(), 'retrieve') !== false) {
    		return $next($request);
		}

        //Do not validate api requests from Neeyamo
        /*
		if (strpos($request->path(), "ney") !== false) {
          return $next($request);
		}
		 * 
		 */
		
		//$message = [];

        if($request["check_type"] == "tri-eye"){
        	$request["check_types"] = [1];
        }else if($request["check_type"] == "single-eye"){
        	$request["check_types"] = [2];
        }else if($request["check_type"] == "state"){
			$request["check_types"] = [3];
		}else if($request["check_type"] == "county"){
			$request["check_types"] = [4];
		}else if($request["check_type"] == "federal_national"){
			$request["check_types"] = [5];
		}else if($request["check_type"] == "federal_state"){
			$request["check_types"] = [6];
		}else if($request["check_type"] == "district"){
			$request["check_types"] = [7];
		}else if($request["check_type"] == "employment"){
			$request["check_types"] = [8];
		}else if($request["check_type"] == "education"){
			$request["check_types"] = [9];
		}else if($request["check_type"] == "mvr"){
			$request["check_types"] = [10];
		}else if($request["check_type"] == "federal-district-tri-eye"){
			$request["check_types"] = [7];
			$request["federal_district_tri_eye_district_ids"] = [json_decode($request["federal_district_tri_eye_district_ids"])];
		}else if($request["check_type"] == "home_auto"){
			$request["check_types"] = [11,13];  //infutor MaxID
		}else if($request["check_type"] == "personal"){
			$request["check_types"] = [12]; //usinfo
		}else if( $request["check_type"] == "cssi_data" ){
			$request["check_types"] = [11,12,13];
		}else if( $request["check_type"] == "cssi_auto" ){
			$request["check_types"] = [11,13]; //infutor auto
		}else if( $request["check_type"] == "cssi_vehicle" ){
			$request["check_types"] = [13]; //infutor auto
		}else if($request["check_type"] == "cssi_mvr"){
			$request["check_types"] = [14];
		}else{
			return response()->json("Invalid check type", 400);
		}
		
		/*
		if(count($message)){
			return response()->json($message, 400);
		}
		 * 
		 */

		return $next($request);
		
	}
	
	
	
}