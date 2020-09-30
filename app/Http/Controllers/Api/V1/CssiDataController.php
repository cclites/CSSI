<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\CssiData;
use App\Models\Profile;
use App\Models\Report;

use App\Jobs\HomeAutoCheck;
use App\Jobs\HomeAutoVinCheck;

use App\Models\Infutor;
use App\Models\InfutorAuto;



// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Other
use Mail;
use Cache;
use DB;
use Log;
use Validator;
use Response;
use Carbon\Carbon;
use View;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use \App\Jobs\UsInfoCheck;
use \App\Jobs\InfutorCheck;
use \App\Jobs\InfutorAutoCheck;

/*
 * Used for CSSI Data api calls.
 * 
 * Functionality here mimics code in V1/CheckCOntroller, but is used for 
 * Home & Auto, Personal, and Auto
 */
class CssiDataController extends Controller{
	
	
	public function data(Request $request){
		
		Log::info("****************************************");
		Log::info("CssiDataController::data");
		Log::info("****************************************");
		
		//Log::info(json_encode($request->all()));
		
		$errorMessages = "";
		
		//if(auth()->user()->sandbox){
			/*
			
		if(0){
			
			Log::info("is sandbox user");
			
			return Response::json(array(
				            'status' => 1,
							'message'=> json_encode($request->all()),
							'tracking'=>null),
			            200
		            );
		}else{
	    */
			
			Log::info("Validate input data.");
			
			/*************************
			 * Validation
			 ************************/
			
			$messages = [
			
				'first_name.required' => "You must provide a First Name to run this check",
				'last_name.required' => "You must provide a Last Name to run this check",
				'address.required' => "You must provide a Street Address to run this check",
				'city.required' => "You must provide a City to run the this check",
				'state.required' => "You must provide a State to run the this check",
				'state.*.exists' => "You must provide a valid State to run the this check",
				'zip.required' => "You must provide a Zip Code to run this check",
				'check_type.required' => "A check type is required"
			
			];
			
			Log::info("Created validation messages");

			 
			//These are the basic rules 
			$rules = [
			    'check_type' => 'required|max:20',
	            'check_types' => 'required|array',
	            'first_name' => 'required|max:50',
	            'last_name' => 'required|max:50',
	            //'address' => 'required|max:200',
	            //'city' => 'required|max:25',
	            //'state' => 'required|max:2',
	            //'zip' => 'required|max:9'
	        ];
			
			Log::info("Created the rules");
			
			$validator = Validator::make($request->all(), $rules, $messages);

	        if ($validator->fails()) {
	        	Log::info("Validation failed");
	        	Log::info($validator->errors());
	        	
	            throw new StoreResourceFailedException('Could not run check', $validator->errors());
	        }
	        
	        Log::info("After validation");
			
			/***********************
			 * Create the check
			 ***********************/
			$check = new Check;
	        $check->user_id = auth()->id();
	        $check->first_name = $request->first_name;
	        $check->last_name = $request->last_name;
			$check->provider_reference_id = createSeed(12); //auto-generated
			$check->sandbox = auth()->user()->sandbox;
			$check->company_id = auth()->user()->company_id;
			
			$check->save();
			Log::info("New check has been saved");
			
			$check_type = new Checktype();
			$check_type->check_id = $check->id;
			
			if( in_array(11, $request["check_types"]) ||  in_array(13, $request["check_types"])){
				$check_type->type_id = 11;
			}elseif( in_array(12, $request["check_types"]) ){
				$check_type->type_id = 12;
			}
			
			$check_type->save();


			/***********************
			 * Create the profile
			 ***********************/
			
			$profile = new Profile;
			
			$params = [
				"first_name" =>$request->first_name,
				"middle_name" =>$request->middle_name ? $request->middle_name : null,
				"last_name" =>$request->last_name,
	    		"birthday" => databaseDate($request->birthday),
				"address" => $request->address,
				"address2" => $request->address2 ? $request->address2 : null,
				"phone" => $request->phone,
				"phone2" => $request->phone2 ? $request->phone2 : null,
				"ip" => $request->ip ? $request->ip : null,
				"ssn" => $request->ssn ? $request->ssn : null,
				"license_state_id" => $request->license_state_id ? $request->license_state_id : null,
				"license_number" => $request->license_number ? $request->license_number : null,
				"city" => $request->city ? $request->city : null,
				"state" => $request->state ? $request->state : null,
				"zip" => $request->zip ? $request->zip : null,
				"email" => $request->email ? $request->email : null
			];
			
			$profile->profile = encrypt(json_encode($params));
			$profile->check_id = $check->id;
			$profile->save();
			
			Log::info("Profile has been created");
			
			$reports = [];
			
			/***********************
			 * Run the checks
			 ***********************/
			//Home & Auto Tri-Eye 
			if (in_array(11, $request["check_types"])) {
				
				Log::info("cssi/data/infutor");
				
				//$infutorCheck = new InfutorCheck($check);
				//die("die");
				
				$homeAutoCheck = new HomeAutoCheck($check);
			    $reports["home_auto"] = $homeAutoCheck->handle();
				 
			}
			
			//Personal Tri-Eye
			if (in_array(12, $request["check_types"])) {
				
				
				Log::info("cssi/data/usinfocheck");
				$usinfoCheck = new UsInfoCheck($check);
			    $reports["personal"] = $usinfoCheck->handle();
				
				//Log::info("Show Result:");
				//Log::info(json_encode($reports["personal"]));
			}
			
			//this was the supplemental car search	
			if (in_array(13, $request["check_types"])) {
				
				Log::info("cssi/data/usinfocheck/13");
				$homeAutoVinCheck = new HomeAutoVinCheck($check);
				
				//json_encode()
				
			    $reports["auto"] = $homeAutoVinCheck->handle();
				
				//the only reason I am running this check is for the VIN info, so I need to combine
				//the records.
				//This is only necessary until the provider combines APIs to include vin in the Id Max data
				//echo "Call combineAuto function\n";
				//$reports = HomeAutoCheck::combineVehicleData($reports);
			}
			
			//echo json_encode($reports["auto"]) . "\n";
			
			$check->is_completed();
			$check->save();
			
			//How do I know which one to return?
			
			/*
			$temp = [];
			
			foreach($reports as $report){
				$temp[] = $report;
			}
			*/
			//$reports = $temp;
			 
			/*
			return Response::json(array(
				              'status' => 1,
							  'message'=> json_encode($reports) 
							),
			            200
		            );
			*/
			//$data = null;
			
			//echo "Check Type " . $request->check_type . "\n";
			
			
			if($request->check_type == "home_auto"){
				//echo "Standardizing home & auto\n";
				//echo json_encode($reports) . "\n";
				$data = CssiData::standardize($reports);
				//echo json_encode($data) . "\n";
			}else{
				
				Log::info("Standardize the report");
				$data = CssiData::standardize($reports);
			}

			$status = 1;

			if( isset($data["error"])){
				$status = 0;
				$message = $data["error"];
			}else{
				$message = $data;
			}

			return Response::json(array(
				            'status' => $status,
							'message'=> $message,
							'tracking'=>$check->provider_reference_id),
			            200
		            );
		    
		//}
			 
	}
	
	public function testData($request){
	
		 $data = json_encode($request->all());
		 
		 return Response::json(array(
				            'status' => 1,
							'message'=> $data,
							'tracking'=>null),
			            200
		            );
			
	}
	
	public function b2bView(Request $request){
		
		//return "<h2>Hello</h2>";
		return View::make('whitelabel/b2bform');
	
		$view = View::make('whitelabel/b2bform');
		$view = (string)$view;
		$view = str_replace(array("\r", "\n", "\t" ), '', $view);
		return $view;
	}
	
	public function capture(Request $request){
		
		Log::info("V1/CssiDataController::capture");
		
		$all = json_encode($request->all());
		
		Log::info($all);
		
		return $all;
		
	}
	
}