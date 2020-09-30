<?php

namespace App\Http\Middleware;

// Models
//use App\Models\Log;
use Log;
use Auth;
use Route;
use JWTAuth;
use Closure;

class ApiAuthMiddleware{
	
	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
    	
		Log::info("ApiAuthMiddleware");
    	
		try {
	
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				Log::info("Unable to authenticate");
				return response()->json(['user_not_found'], 400);
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
		
		/*
		if(Auth::user()->company_id == 'zTcphM'){
			return response()->json(['error'], "API is not available.");
		}*/
		
		//Set a flag so other parts of the app know this request came through the API.
		Auth::user()->apiuser = true;
		
		$path = $request->url();

        //If we are doing b2b screening, we don't want to short circuit in the 
        //check controller. Hacky, yes, but it does the job.
		if (strpos($path, "btob") !== false) {
			Log::info("Unset apiuser");
		    unset(Auth::user()->apiuser);
		}

		return $next($request);	
	}
	
	
	public function register($request){
		
		//pass on to the AuthController
		
	}
	
	
	
	
	
	
	
	
	
	
}
