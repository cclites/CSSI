<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use DB;
use Log;

class TouAcceptance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	
    	//Bypass for 4SafeDrivers and Neeyamo
    	if( Auth::user()->company_id == "zTcphM" || Auth::user()->company_id == "eCJYD1"){
    		//Log::info("Bypassing TOU Acceptance");
    		return $next($request);
    	}
		
		$record = DB::table('tou_accept')->where('user_id', Auth::user()->id)->first();
		
		if($record){
			Log::info("User has accepted TOU already");
			return $next($request);
		}else{
			Log::info("Redirect to the show page");
		    return redirect("tou/notice/show");
		}
		
        
    }
}
