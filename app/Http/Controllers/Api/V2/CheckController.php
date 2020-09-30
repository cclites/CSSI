<?php

namespace App\Http\Controllers\Api\V2;

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

//checks library
use App\Http\Controllers\Library\Api\ChecksLibrary;
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
	
    

    public function store(Request $request)
    { 	
	     
		//When running B to C checks, the check types come as an array of strings
		//This needs to be corrected in the B to C code
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
		//***************************************************

		//represents error messages
		$messages = $this->messages->messages(); //load the messages
		
		//represents basic rules
		$rules = $this->validate->basicRules();  //load the basic rules
		
		//$rules = $this->buildRules($request->check_types, $rules);
		$isValid = $this->validateRules($request->all(), $rules, $messages);
		
		if(!$isValid){
			
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
		
        $check = new Check;
        $check->user_id = auth()->id();
        $check->first_name = $request->first_name;
        $check->middle_name = $request->middle_name;
        $check->last_name = $request->last_name;
		$check->provider_reference_id = createSeed(12); //auto-generated
		$check->sandbox = auth()->user()->sandbox;
		$check->company_id = auth()->user()->company_id;
		
		//hide check from all users
		if(auth()->user()->hasRole('admin') && isset($request->hide_check)){
			$check->viewable = false;
		}
		
		$check->save();
		
		
		
		/*
		 * This is a temporary patch to add grab checks on the fly and convert them
		 * to the new format.
		*/
		 
		$order = null;
		 
		try{
			$order = convertCheckToOrder($check);
		}catch(\Exception $e){
			Log::info("Unable to convert check.");
			Log::info(json_encode($check));
			Log::info($e->getMessage());
		}
		
		
		
				
		//save the distinct token to prevent duplicate checks
		if(isset($request->distinct)){
			DB::table("check_distinct")->insert(['token' => $request->distinct, 'check_id'=>$check->id]);
		}

		$profile->profile = encrypt(json_encode($params));
		$profile->check_id = $check->id;
		$profile->save();
	
        return $this->response->item($check, new CheckTransformer);
	
    }

	public function validateRules($request, $rules, $messages){
				
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

		if (is_array($request->check_types) AND in_array(11, $request->check_types)) {
			
        }

		//TODO: Validation rules for UsInfo if they ever getr added to portal
		if(is_array($request->check_types) && in_array(12, $request->check_types)){
			
		}
		
		$validator = Validator::make($request->all(), $rules, $messages);
		return $validator->fails();
	
	}
	
	public function createSingleCheck($order){
		
		
		
	}
	
}

