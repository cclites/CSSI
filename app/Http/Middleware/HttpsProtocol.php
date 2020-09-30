<?php


namespace App\Http\Middleware;


use Closure;
use Log;

class HttpsProtocol {


    public function handle($request, Closure $next)

    {

            if (!$request->secure()) {

                Log::info("THIS IS NOT A SECURE REQUEST");
                return redirect()->secure($request->getRequestUri());

            }else{
            	
				Log::info("THIS IS A SECURE REQUEST");
            }


            return $next($request); 

    }

}


?>