<?php


namespace App\Http\Middleware;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Auth;
use Closure;
use Log;
use App\Models\Type;

class CheckEnabled {


    public function handle($request, Closure $next){
    	
		Log::info("Show request in checkEnabled");
		Log::info( json_encode( $request ) );
    	
		$types = Type::all();
		$disabledTypes = $types->where('enabled', false);

		$disabledTypeIds = [];
		$messages = [];
		
		foreach($disabledTypes as $type){
			
			$disabledTypeIds[] = $type->id;
			$messages[] = "The " . $type->title . " is disabled.";
			

		}
		
		//At least one of the checks is disabled.
		if(is_array($request->check_types) && array_intersect($request->check_types, $disabledTypeIds)){
			
			$errorMessage = "";
			
			foreach($messages as $message){
				$errorMessage .= "$message,";
			}
			
			if(Auth::user()->apiuser){
				
				return response()->json([
				  'message'=> "There was an error with your check.",
				  'status' => -1,
				  'tracking' => null,
				  'data' => $errorMessage
				], 400);
				
				
			}else{
				Log::info("Is not api user.");
				flash("The current checks are disabled.<br>" . $errorMessage . " Please try again later.");
			    return back();
			}
			
			
			
		}
				
		return $next($request);

    }

}


?>