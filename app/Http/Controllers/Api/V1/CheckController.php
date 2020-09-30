<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\Check;
use App\Models\Checktype;
use App\Models\Employment;
use App\Models\Education;
use App\Models\Mvr;
use App\Models\MvrState;
use App\Models\State;
use App\Models\Report;
use App\Models\Company;
use App\Models\Profile;
use App\Models\UsInfoSearch;
use App\Models\Infutor;
use App\Models\InfutorAuto;
use App\Models\CssiData;
use Auth;

// Transformers
use \App\Transformers\Api\V1\CheckTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Storage;

// Jobs
use App\Jobs\NationalTriEyeCheck;
use App\Jobs\NewNationalTriEyeCheck;
use App\Jobs\NationalSingleEyeCheck;
use App\Jobs\StateTriEyeCheck;
use App\Jobs\CountyTriEyeCheck;
use App\Jobs\FederalNationalTriEyeCheck;
use App\Jobs\FederalStateTriEyeCheck;
use App\Jobs\FederalDistrictTriEyeCheck;
use App\Jobs\EmploymentCheck;
use App\Jobs\EducationCheck;
use App\Jobs\MvrCheck;
use App\Jobs\MvrCheckInstant;

use App\Jobs\HomeAutoCheck;

use App\Jobs\UsInfoCheck;
use App\Jobs\InfutorCheck;
use App\Jobs\InfutorAutoCheck;

use App\Jobs\BulkNationalTriEyeCheck;

//Libraries
use App\Http\Controllers\Library\Api\ChecksLibrary;
use App\Http\Controllers\Library\Api\OrdersLibrary;
use App\Http\Controllers\Library\Api\ValidationRulesLibrary;
use App\Http\Controllers\Library\Api\ValidationMessagesLibrary;


// Other
use DB;
use Log;
use Excel;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Crypt;

class CheckController extends Controller
{
	
	protected $checkLib = "";
	protected $validate = "";
	protected $messages = "";
	
	public function __construct(){
		$this->checkLib = new ChecksLibrary();
		$this->validate = new ValidationRulesLibrary();
		$this->messages = new ValidationMessagesLibrary();
	}
	
    public function index(Request $request, $id = null)
    {
        Log::info("******** In Api/V1/CheckController::index  ******************");

        //Limited admins need a limited set of checks. The checks are returned
        //immediately.
		if(Auth::user()->hasRole('limited_admin')){
			$checks = $this->limitedAdminChecks($request);
			$checks = $checks->orderBy('created_at', 'DESC')->paginate(25);
			return $this->response->paginator($checks, new CheckTransformer);
		}
		
		//Otherwise, get all the checks for the company
		$checks = Check::where('checks.company_id', auth()->user()->company_id);
		
		
		//Dynamic search
		/*********************************************************************/
		if($request->q) {
			
			$searchString = explode(" ", $request->search);
			
			foreach($searchString as $str){
				$checks = $checks->where(function($query) use ($str){
					
					$query->where('checks.first_name', 'like', '%'.$str.'%')
					      ->orWhere('checks.last_name', 'like', '%'.$str.'%')
						  ->orWhere('checks.user_id', 'like', '%'.$str.'%');
				});	
			}
		}
		/*********************************************************************/
		
		
        if ($request->ids) {
            $checks = $checks->whereIn('checks.id', $request->ids);
        }
        if($request->first_name) {
            $checks = $checks->where('checks.first_name', 'like', '%'.$request->first_name.'%');
        }
        if($request->middle_name) {
            $checks = $checks->where('checks.middle_name', 'like', '%'.$request->middle_name.'%');
        }
        if($request->last_name) {
            $checks = $checks->where('checks.last_name', 'like', '%'.$request->last_name.'%');
        }

        // Date Filter
        if ($request->min_date) {
            $checks = $checks->where('checks.created_at', '>=', databaseDate($request->min_date));
        }
        if ($request->max_date) {
            $checks = $checks->where('checks.created_at', '<=', databaseDate(date('Y-m-d', strtotime($request->max_date.' +1 Day'))) );
        }

        $checks = $checks
            ->orderBy('id', 'desc')
            ->paginate(25);

        return $this->response->paginator($checks, new CheckTransformer);
    }

    public function show($id)
    {
    	
		Log::info("In Api/V1/CheckController");
		
		if( Auth::user()->hasRole('limited_admin') && Auth::check()){
			Log::info("Is limited admin");
			$check = Check::find($id);
		}else{
			
			Log::info("Is not limited admin - ");
			$check = Check::where('id', $id)
					->where('active', true)
	            	->first();
			/*
			$check = Check::where( function($q){ $q->where('user_id', auth()->id() )->orWhere('company_id', auth()->user()->company_id); })
	            ->where('id', $id)
				->where('active', true)
	            ->first();
			 */
		}
		
		Log::info(json_encode($check));
	
        if (!$check) {
        	Log::info("Check is not found");
            return $this->response->errorNotFound();
        }

        return $this->response->item($check, new CheckTransformer);
    }
	
	//Grab just the checks the limited admin can see.
	public function limitedAdminChecks(Request $request){
		
		return $this->checkLib->checksForLimitedAdmin($request);
		
	}
	
	public function retrieve(Request $request){
		
		Log::info("CheckController::retrieve");

		$report = Report::where('tracking', strtoupper($request->id))
            ->first();
			
		if($report){
			return decrypt($report->report);
		}else{
			return [];
		}
			
		
	}

    public function store(Request $request)
    { 	
	     
		//When running B to B checks, the check types come as an array of strings
		//Need to convert
		if(gettype($request->check_types[0]) == 'string'){
			$request->check_types = $this->checkLib->convertStringArrayToIntArray($request->check_types);
		}
		 
		//Each check should have a distinct and unique id.
		if( isset($request->distinct) && !$this->checkLib->idIsDistinct($request->distinct) ){

			if(auth()->user()->apiuser){
				
				return Response::json(array(
				            'status' => -1,
				            'message' => "There was an error with your check.",
				            'data' => "distinct flag not unique",
							'tracking' => null),
				            200
				        );
				
			}else{
				
				flash("This check is not distinct.");
				return back();
			}				
		}

		/***************************************************************************************/
		
		$messages = $this->messages->messages(); 
		$rules = $this->validate->basicRules();
		
		/************************* BUILD THE RULES****************************************************/
        if ( is_array($request->check_types) AND !empty(array_intersect([
                1,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
            ], $request->check_types)
        )) {
			$rules = $this->validate->ssnRules($rules);
        }

      
        if (is_array($request->check_types) AND in_array(3, $request->check_types)) {
        			
        	if( isset($request->state_tri_eye_state_ids) && gettype($request->state_tri_eye_state_ids) == "string"){
				$temp = explode(",", $request->state_tri_eye_state_ids);
				$requestData = $request->all();
				$requestData["state_tri_eye_state_ids"] = $temp;
				$request = new \Illuminate\Http\Request($requestData);
			}	
        	
			$rules = $this->validate->stateTriEyeRules($rules);
        }

        if (is_array($request->check_types) AND in_array(4, $request->check_types)) {
        	
			if( isset($request->county_tri_eye_county_ids) && gettype($request->county_tri_eye_county_ids) == "string"){
				$temp = explode(",", $request->county_tri_eye_county_ids);
				$requestData = $request->all();
				$requestData["county_tri_eye_county_ids"] = $temp;
				$request = new \Illuminate\Http\Request($requestData);
			}
			
			$rules = $this->validate->countyTriEyeRules($rules);
        }

        if (is_array($request->check_types) AND in_array(6, $request->check_types)) {

			if( isset($request->federal_state_tri_eye_state_ids) && gettype($request->federal_state_tri_eye_state_ids) == "string"){
				$temp = explode(",", $request->federal_state_tri_eye_state_ids);
				$requestData = $request->all();
				$requestData["federal_state_tri_eye_state_ids"] = $temp;
				$request = new \Illuminate\Http\Request($requestData);
			}
			 
			$rules = $this->validate->federalStateTriEyeRules($rules);
        }

        if (is_array($request->check_types) AND in_array(7, $request->check_types)) {
        	
			if( isset($request->federal_district_tri_eye_district_ids) && gettype($request->federal_district_tri_eye_district_ids) == "string"){
				$temp = explode(",", $request->federal_district_tri_eye_district_ids);
				$requestData = $request->all();
				$requestData["federal_district_tri_eye_district_ids"] = $temp;
				$request = new \Illuminate\Http\Request($requestData);
			}
			
			$rules = $this->validate->federalDistrictRules($rules);
        }
		
		//Employment
		if (is_array($request->check_types) AND in_array(8, $request->check_types)) {
			$rules = $this->validate->employmentRules($rules);
        }
		
		if (is_array($request->check_types) AND in_array(9, $request->check_types)) {
			$rules = $this->validate->educationRules($rules);
        }

        if (is_array($request->check_types) AND in_array(10, $request->check_types)) {
			$rules = $this->validate->mvrRules($rules);
        }
		
		//TODO
		if (is_array($request->check_types) AND in_array(11, $request->check_types)) {
			
        }

		//TODO
		if(is_array($request->check_types) && in_array(12, $request->check_types)){
			
		}
		
		//TODO
		if(is_array($request->check_types) && in_array(13, $request->check_types)){
			
		}
		
		//TODO
		if(is_array($request->check_types) && in_array(14, $request->check_types)){
			
		}
		
		
		/************************** VALIDATE ***********************************/

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
        	
			if(auth()->user()->apiuser){
				
				return Response::json(array(
					            'status' => 0,
					            'errors' => $validator->errors()),
					            200
					        );
			}
			
            throw new StoreResourceFailedException('Could not run check', $validator->errors());
        }
		
		
		$profile = new Profile;
		$params = $profile->createProfileData($request);
		
		//TODO: Create _Order instead, and then create a check for each.
		
		//$order = new \App\_Models\Order();
		$order = createOrder($request);
		
        /*
        $check = new Check;
        $check->user_id = auth()->id();
        $check->first_name = $request->first_name;
        $check->middle_name = $request->middle_name;
        $check->last_name = $request->last_name;
		$check->provider_reference_id = createSeed(12); //auto-generated
		$check->sandbox = auth()->user()->sandbox;
		$check->company_id = auth()->user()->company_id;
		 
		 
		 
		if(auth()->user()->hasRole('admin') && isset($request->hide_check)){
			//Log::info("Set check visibility to false");
			$check->viewable = false;
		}else{
			//Log::info("Leave check visible");
		}
		
		$check->save();
		
		*/
		
		/*
		 * This is a temporary patch to add grab checks on the fly and convert them
		 * to the new format.
		 */
		
		
		/*
		$order = null;
		 
		try{
			$order = convertCheckToOrder($check);
		}catch(\Exception $e){
			Log::info("Unable to convert check.");
			Log::info(json_encode($check));
			Log::info($e->getMessage());
		}
		*/
		
		//save the distinct token to prevent duplicate checks
		if(isset($request->distinct)){
			DB::table("check_distinct")->insert(['token' => $request->distinct, 'check_id'=>$check->id]);
		}

		$profile->profile = encrypt(json_encode($params));
		
		//TODO: convert to create an order
		
		//$profile->check_id = $check->id;
		$profile->order_id = $order->id;
		$profile->save();
			

        /**** START CHECKS ***/
        if (in_array(1, $request->check_types)) {
        	
			Log::info("Running NEW National Tri-Eye check");
			
			
            //$check->types()->attach(1);
			//$this->addToDailies(1, $check->id);
			//CREATE A NEW CHECK
			
			$check = new App\_Models\Check($params, $order, 1);
			
			$response = dispatch(new NewNationalTriEyeCheck($check));
			
			
			
			/*
			 * When users make a request through the API, an 'apiuser' flag is set on the user object.
			 * API users can only run one check per request.
			 */
            
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => json_encode($response)),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => -1,
				            'message' => "Unable to process your request."),
				            200
				        );
			}
			
        }
		
		
        if (in_array(2, $request->check_types)) {
        	
			Log::info("Running National Single Eye Check");
            $check->types()->attach(2);
			$this->addToDailies(2,$check->id);
			
			try{
				if(auth()->user()->apiuser){
					
					$si = new NationalSingleEyeCheck($check);
					$report = $si->handle();
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => json_encode($report)),
					            200
					        );
				}else{
					dispatch(new NationalSingleEyeCheck($check));
				}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => -1,
				            'message' => "Unable to process that request",
							'tracking' => null),
				            200
				        );
			}
			
        }

        if (in_array(3, $request->check_types)) {
        	
        	Log::info("Running State Tri Eye Check");
            $check->types()->attach(3);
			$this->addToDailies(3, $check->id);
			
            $check->states()->attach($request->state_tri_eye_state_ids);
            $response = dispatch(new StateTriEyeCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
			
        }
		
        if (in_array(4, $request->check_types)) {
        	
			Log::info("Running County Tri Eye Check");
            $check->types()->attach(4);
			$this->addToDailies(4, $check->id);
			
			$temp = $request->county_tri_eye_state;
            $check->counties()->attach($request->county_tri_eye_county_ids);
            $response = dispatch(new CountyTriEyeCheck($check, $temp));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
        }
		
        if (in_array(5, $request->check_types)) {
        		
        	Log::info("Running Federal National Tri Eye Check");
            $check->types()->attach(5);
            $this->addToDailies(5, $check->id);
            $response = dispatch(new FederalNationalTriEyeCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
        }
		
        if (in_array(6, $request->check_types)) {
        	
			Log::info("Running Federal State Tri Eye Check");
            $check->types()->attach(6);
			$this->addToDailies(6, $check->id);
            $check->federal_states()->attach($request->federal_state_tri_eye_state_ids);
            $response = dispatch(new FederalStateTriEyeCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
        }
		
        if (in_array(7, $request->check_types)) {
        	
			Log::info("Running Federal District Tri Eye Check");
            $check->types()->attach(7);
			$this->addToDailies(7, $check->id);
            $check->districts()->attach($request->federal_district_tri_eye_district_ids);
            $response = dispatch(new FederalDistrictTriEyeCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}

        }
		
		//TODO: This will be handled by an API
        if (in_array(8, $request->check_types)) {
        	
			Log::info("Running Employment check");
            $check->types()->attach(8);
            $this->addToDailies(8, $check->id);
            $response = dispatch(new EmploymentCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
        }

        //TODO: This will be handled by an API
        if (in_array(9, $request->check_types)) {
        	
			Log::info("Running Education check");
            $check->types()->attach(9);
			$this->addToDailies(9, $check->id);
            $response = dispatch(new EducationCheck($check));
			
			try{
				if(auth()->user()->apiuser){
					
					return Response::json(array(
						  		'tracking' => $check->provider_reference_id,
					            'status' => 1,
					            'message' => "You will receive an email when your report is ready." ),
					            200
					        );
					}
			}catch(\Exception $e){
				
				return Response::json(array(
				            'status' => 0,
				            'message' => "Unable to process that request"),
				            200
				        );
			}
			
        }

        if (in_array(10, $request->check_types)) {
        	
			Log::info("Running MVR check");
			$check->types()->attach(10);
			$this->addToDailies(10, $check->id);
			//This adds the MVR to check_state table. I don't want that though
			//because it mixes national state checks
			$check->states()->attach($request->license_state_id);
			
			//This seperates out the mvrs -  
			$check->mvr_states()->attach($request->license_state_id);
			

			$overnight = array(2, 12, 26);
			//$state = $check->states[0];
			$state = cache('states')->find($request->license_state_id);
			
			
			$message = "Request is complete.";
			$status = 1;
			
			//cache('states')
			
			Log::info("Run the instant check.");

			//don't do this if it is an overnight check.
			if(auth()->user()->apiuser && !in_array($request->license_state_id, $overnight)){
				
				$mvrCheck = new MvrCheckInstant($check);
				
				$standardizedResults = $mvrCheck->handle();
					
				if(isset($standardizedResults["error"])){
					$message = $standardizedResults["description"];
					$status = $standardizedResults["status"];
					$data = $standardizedResults["error"];
				}else{
					$data = json_encode($standardizedResults);
				}
	
				return Response::json(array(
			  		'tracking' => strtoupper($check->provider_reference_id),
		            'status' => $status,
			        'message' => $message,
					'data'=> $data),
		            200
		        );

			}else{
				
				Log::info("Not using instant MVR");
			 		
			 	$response = dispatch(new MvrCheck($check));
			 
			    if(auth()->user()->apiuser){
			    	
			    	$message = "Your check will be available in a couple of minutes.";
					$status = 1;
		
					if(!$response){
						$message = "Unable to process that request";
						$status = -1;
					}else if(!is_object($response) && $response == 1){
				  		$message = "There was an error with your request";
						$status = 0;
				  	}else if (in_array($request->license_state_id, $overnight) ){
				  		$message = "Your check will be available within 24 hours";
						$status = 1;
				  	}
					
				  	return Response::json(array(
				  		'tracking' => strtoupper($check->provider_reference_id),
			            'status' => $status,
						'message'=> $message),
			            200
			        );
					
			    }

			}
			 
			
        }

        // API check only
        if (in_array(11, $request->check_types)) {
        	
        	Log::info("Running Home & Auto Tri Eye check");
			$check->types()->attach(11);
			$this->addToDailies(11, $check->id);
			
			$response = dispatch(new InfutorCheck($check));
			
			Log::info("Logging response");
			
			return Response::json(array(
			  		'tracking' => strtoupper($check->provider_reference_id),
		            'status' => 1,
					'message'=> $response),
		            200
		        );
	
        }
		
		
		// API check only
		if (in_array(12, $request->check_types)) {
			
			Log::info("Running Personal Tri Eye check");
			$check->types()->attach(12);
			$this->addToDailies(12, $check->id);
			
			if(auth()->user()->apiuser){
				//run immediately
				
				//$response = dispatchNow(new UsInfoCheck($check));
				
				$usInfoCheck = new UsInfoCheck($check);
				$response = $usInfoCheck->handle();
				
				Log::info("Would like to see a response here");
				
				//$response = json_encode($response);
				
				return Response::json(array(
			  		'tracking' => strtoupper($check->provider_reference_id),
		            'status' => 1,
					'message'=> $response),
		            200
		        );

			}else{
				$response = dispatch(new UsInfoCheck($check));
			}

        }
		
		// API check only
		if (in_array(13, $request->check_types)) {
			
			Log::info("Running Auto Tri Eye check");
			$check->types()->attach(13);
			$this->addToDailies(13, $check->id);
			$response = dispatch(new InfutorAutoCheck($check));			
        }
		
		if (in_array(14, $request->check_types)) {
			
			Log::info("CSSI_MVR");
			$check->types()->attach(14);
			$this->addToDailies(14, $check->id);
			
			return json_encode($request->all());
			//$response = dispatch(new InfutorAutoCheck($check));		
        }

 
		try{
			
			createChecksFromOrder($order);
			Log::info("Creating check from order");
			Log::info(json_encode($order));
		}catch(\Exception $e){
			Log::info("Unable to capture check");
			Log::info(json_encode($order));
			Log::info($e->getMessage());
		}
		
        return $this->response->item($check, new CheckTransformer);
	
    }

	
	/*
	 * When user enters information for a B to B check, it is saved here.
	 */
	public function btob(Request $request){
		
		Log::info("V1/CheckController::btob");
		Log::info($request->id);
		
		Log::info(json_encode($request->all()));
		DB::table('b_to_b')->where('check_request_id', $request->id)->update(['active'=>false]);

		$this->store($request);
		return view('whitelabel/thankyou');
	}
	
	/*
	 * Bulk Imports
	 */
	public function import(Request $request){
		
		Log::info("V1/CheckController::import");
		
		$errors = $this->checkLib->validateCvsUploadFileType();
		
		if( count($errors) ){
			return $errors;
		}else{
			
			if($request->type == 1){
				$errors = $this->checkLib->validateBulkTriEye();
			}
			
			if( count($errors) ){
				return $errors;
			}
		}
		
		$file = file_get_contents($_FILES['import']['tmp_name']);
		$path = "batch/" . Auth::user()->company_id . ".json";
		$result = Storage::put($path, $file);
		
		return 0;

	}
	
	public function addToDailies($typeId, $checkId){
		
		try{
				
			$day = Carbon::today();
			$day = $day->format("Ymd");
			
			$daily = DB::table('_dailies')
					  ->where('type', $typeId)
					  ->where('day', $day)
					  ->first();
					  
			if($daily){

				$checks = json_decode($daily->checks);
				$checks[] = $checkId;
				$checks = json_encode($checks);
				
				
				$total = $daily->total += 1;
				
				DB::table('_dailies')
					  ->where('type', $typeId)
					  ->where('day', $day)
					  ->update(['total'=> $total, 'checks'=>$checks] );
					  
			}else{
				
				$checks[] = $checkId;
				$checks = json_encode($checks);
				
				DB::table('_dailies')->insert(
    				['day' => $day, 'type' => $typeId, 'total' => 1, 'checks'=>$checks]
				);
			}
			
			//Log::info("Doing something");		  
			//Log::info(json_encode($daily));
			
			
		}catch(\Exception $e){
			Log::info($e->getMessage());
			Log::info("ded");
			return;
		}
		
		

    }


}

