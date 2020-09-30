<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Auth;
use Closure;
use Log;
use Response;

use App\Http\Controllers\Library\Api\ChecksLibrary;

class OrderIsDistinct {


    public function handle($request, Closure $next){
    	
		$checkLib = new ChecksLibrary();
    	
		if( $checkLib->idIsDistinct($request->distinct) ){

			if(auth()->user()->apiuser){
				
				return Response::json(array(
				            'status' => -1,
				            'message' => "There was an error with your check.",
				            'data' => "distinct flag not unique",
							'tracking' => null),
				            200
				        );
				
			}else{
				
				flash("This order has already been submitted.");
				return back();
			}				
		}

    }

}


?>