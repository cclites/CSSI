<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;
use App\Models\Profile;
use App\Models\Check;
use App\Models\State;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;
use Parser;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use View;
use PDF;
use Crypt;
use DB;



class CheckController extends Controller
{
	public function index(Request $request)
	{
		
		Log::info('***** Controllers/CHECK CONTROLLER');
		
        try {
            $checks = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/checks');
        } catch (ResourceException $e) {
        	Log::info("Catch error");
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

		return view('checks/index')
            ->with('request', $request)
            ->with('checks', $checks);
	}

    public function create()
    {
    	Log::info("Controllers/CheckController::create()");
        return view('checks/create');
    }

    public function input(Request $request)
    {
        if ($request->check_id) {
            $check = $this->api
                ->be(auth()->user())
                ->get('api/checks/'.$id);    
        }
        else {
            $check = null;
        }
        
        return view('checks/input')
            ->with('request', $request)
            ->with('check', $check);
    }

    public function store(Request $request)
    {

        try {

            $result = $this->api
		              ->be(auth()->user())
		              ->with($request->all())
		              ->post('api/checks/'. $request->type);
					  	
        } catch (StoreResourceFailedException $e) {
        	
			if($request->check_type == "cssi_data"){
				
			}else{
				return back()
	            ->withErrors($e->getErrors())
	            ->withInput($request->all());
			}   
        }
		
		if($request->check_type == "cssi_data"){
			return $result;
		}else{

			if(isset($result["error"]) && $result["error"]){
				flash("This is a duplicate of check " . $result["id"]);
				//return redirect( 'checks/' . $result["id"] );
				
				cLog("Captured duplicate check.", 'app/v1', 'checkController');				
				
			}else{
				flash('Your request has been sumitted successfully');
	            //return redirect(secure_url('checks'));
			}
			
			return redirect(secure_url('checks'));

		}

    }

    public function show(Request $request, $id, $viewType = 'html'){
    	
		//Log::info("CheckController::show()");
		//Log::info("Getting checks that belong to this user/company");
		//Log::info("Id is $id");
		
		
		
		try{
			
			if(!is_numeric($id)){
				Log::info($id);
				$chk = Check::where('provider_reference_id', $id)->first();
				$id = $chk->id;
				Log::info($id);
			}
			
		}catch(\Exception $e){
			
			Log::info("unable to find result.");
			
		}

        $check = $this->api
            ->be(auth()->user())
            ->get('api/checks/'. $id);
			
		Log::info("Returned check");
			
        $check->report = $check->report();
        
		Log::info("viewType is $viewType");
		
        //never used
        if ($viewType == 'parsed') {
            dd($check->parsed_results);
        }else if ($viewType == 'standardized') {
            dd($check->standardized_results);
        }else if($viewType == 'pdf'){
        	
			Log::info("Generate PDF");

			$checkHtml = View::make('checks/pdf', ['check' => $check])->render();
			return PDF::loadHTML($checkHtml)->setPaper('a4')->setOption('user-style-sheet',  public_path('css/printChecks.css') )->inline();
		}else if($viewType == 'html'){
					
			Log::info("Generate HTML");	
			
			return view('checks/show')
	            ->with('check', $check);
		}

	        

    }
	
	//Don't think this is used.
	public function printable($id){
		
		$check = $this->api
            ->be(auth()->user())
            ->get('api/checks/'.$id);
			
	}
	
	//import comma delimited list of users
	public function import(Request $request){
		
		Log::info("Importing a file");
		
		try {

            $result = $this->api
		              ->be(auth()->user())
		              ->with($request->all())
		              ->post('api/import');
					  	
        } catch (StoreResourceFailedException $e) {
            flash('There was an error with your upload.')->important();
			return back();
        }
		
		flash('Your file has been uploaded. An email will be sent when your checks are ready');
	    return redirect(secure_url('checks/create'));
		
	}
	
	public function profile(Request $request){
		
		$checkId = $request->checkId;
		
		$check = Check::where('id', $checkId)->first();
		
		if(isset($check->profile)){

			$tempProfile = Crypt::decrypt($check->profile->profile);			
			$profileJson = json_decode($tempProfile);
			
			Log::info( json_encode($profileJson) );
			
            if( isset( $profileJson->license_state_id ) ){
            	
				//$stateAbbrv =  $profileJson->license_state_id;
				//Log::info("State Abbrv: " . $stateAbbrv);
				
				//$state = State::where('code', $stateAbbrv)->first();
				//Log::info("State id is " . $state->id);
				
            }
			//crap. I need to convert the state to an id.
			
			
			
			
			//Log::info( json_encode($tempProfile->state) );
			
			//$state = State::where('code', $stateAbbvr)->first();
			
			//$tempProfile->state = $state->id;
			
			
			$check->profile->profile = $tempProfile;
		}else{
			Log::info("Something went wrong.");
		}
		
		return json_encode($check);
		
	}

}