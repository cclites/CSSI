<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use JWTAuth;

class CssiAppMiddleware{
	
	/*
	 * *******************************************************************************************************************************************
	 * *******************************************************************************************************************************************
	 */
	
	//Not used at the moment, but once the mobile app is further along, it will be.
	public function handle($request, Closure $next){
		
		Log::info("CssiAppMiddleWare");
		Log::info(json_encode(getallheaders()));
		
		//$user = JWTAuth::parseToken()->authenticate();
		
		//Log::info(json_encode($user));
		
		//$header = $request->header('Authorization');
		//Log::info(json_encode($request->header()));
		//JWTAuth::setToken($request->token);
		
		//$user = JWTAuth::parseToken()->authenticate();
		
        //$s = print_r($request, true);
		//Log::info($s);

		$message = "Made it to CSSI Middleware";
		return response()->json($message, 200);
		
		//return $next($request);
		
	}
	
}
