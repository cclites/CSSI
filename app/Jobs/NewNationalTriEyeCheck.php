<?php

namespace App\Jobs;

use App\Models\DataDiver;
use App\Models\Check;
use App\Models\Report;
use App\Http\Controllers\ReportController;

use App\_Models\Order;

use \Carbon\Carbon;
use Log;
use Crypt;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewNationalTriEyeCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	protected $order;

    /*
    public function __construct(Check $check)
    {
        $this->check = $check;
    }*/
    
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
    	
        $order = $this->order;
		$profile = new \stdClass();
        
        if($profile){
			$profile = json_decode(Crypt::decrypt($order->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		}else{
			//should return an error here. Profile MUST exist, or this won't work.
			//TODO: throw an error
		}

        // load query XML template
        $queryXML = simplexml_load_file(config_path("data_divers/national-single-eye-request-template.xml"));
        
        // Update XML
        $queryXML->SubjectDetail->FirstName = $order->first_name;
        $queryXML->SubjectDetail->LastName = $order->last_name;
		

		if ($profile->ssn) {
            $queryXML->SubjectDetail->SSN = $profile->ssn;
        }
        else {
            unset($queryXML->SubjectDetail->SSN);
        }
        
		if($profile->range && $profile->range !== ""){
		  $queryXML->ProcessingRules->InstantCriminalRules->ExcludeRecords->YearRange = $profile->range;
		}

		$queryXML->SubjectDetail->DOBMonth = $profile->birthday->format('m');
        $queryXML->SubjectDetail->DOBDay = $profile->birthday->format('d');
        $queryXML->SubjectDetail->DOBYear = $profile->birthday->format('Y');
		 
		//move to configs 
        $queryXML->Authentication->Username = /*DataDiver::user();*/ env("DATADIVERS_USERNAME");
        $queryXML->Authentication->Password = /*DataDiver::password();*/ env("DATADIVERS_PASSWORD");

        // save the updated document
        $request = array('query' => $queryXML->asXML());
        $payload = http_build_query($request);

        $ch = curl_init();
        
        // set up the options
        curl_setopt($ch, CURLOPT_URL, DataDiver::wsdl()); // specify URL
        curl_setopt($ch, CURLOPT_POST, 1); // Use POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // this will have it return a string on exec call
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // time to allow it to connect
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // time to allow it to send
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);

        $singleEye = curl_exec($ch); // perform the post
        
        
        if($errno = curl_errno($ch)) {
			
			Log::info("DataDiver error");
		    $error_message = curl_strerror($errno);
		    Log::info("cURL error ({$errno}): {$error_message}");

			//System will need to redo the check request
			//This needs to return an error
			//TODO: throw an error
			
		}
        
        curl_close($ch); // close the session
        
        //Now get the USInfo data
        
        $state = $profile->state;
		
		
		$params = [
		   "username" => env('USINFO_USER'),
		   "password" => env('USINFO_PASS'),
		   "firstName" => $order->first_name,
		   "middleName" => $order->middle_name,
		   "lastName" => $order->last_name,
		   "DOB" => $profile->birthday,
		   "SSN" => $profile->ssn,
		   "state" => $state,
		   "city" => $profile->city,
		   "zip" => $profile->zip,
		   "address" => $profile->address,
		   "phone" => $profile->phone,
		   "email" => $profile->email,
		   "clientReference" => strtoupper($order->provider_reference_id),
		];
        
		/*
        if($profile->birthday){
			$bday = explode("-", $profile->birthday);
		    $profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
			$params["DOB"] = $profile->birthday;
		}
		*/
		
		
		$payload = http_build_query($params);
		$url = env('USINFO_URL') . $payload;
		$ch = curl_init();
        
        // set up the options
        curl_setopt($ch, CURLOPT_URL, $url); // specify URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // this will have it return a string on exec call
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // time to allow it to connect
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // time to allow it to send
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);

        $usInfo = curl_exec($ch); // perform the post
        
        if($errno = curl_errno($ch)) {
			
			Log::info("UsInfo error");
		    $error_message = curl_strerror($errno);
		    Log::info("cURL error ({$errno}): {$error_message}");
			
			//This needsto return an error

			//System will need to redo the check request
			//TODO: throw an error
		}
        
        curl_close($ch); // close the session
        
        //If usinfo doesn't start with a an opening < tag, set $usInfo = []
        
        //Log::info("****** SINGLE EYE ********");
		//Log::info(json_encode($singleEye));
		
		//Log::info("******* USINFO ***********");
		//Log::info(json_encode($usInfo));
        
        //standardize the checks
        $standardized = DataDiver::TriEyeCombineChecks($singleEye, $usInfo);
		
		
				
		$report = new \App\Models\Report;
		
		if(isset($standardized->xml)){
			//Log::info("Standardized XML is set");
			$report->report = encrypt(json_encode($standardized->xml));
		}else{
			$report->report = encrypt(json_encode($standardized));
		}

		$report->order_id = $order->id;
		$report->tracking = strtoupper($order->provider_reference_id); //RENAME TRACKING COLUMN??
		$report->check_type = 1; //WILL I STILL NEED CHECK_TYPE?
		$report->save();

        //WILL I STILL NEED A CHECK TYPE?

        $checktype = $check->checktypes->where('type_id', 1)->first();
        $checktype->is_completed();
		$checktype->save();

        //TODO: This doesn't seem to work
		if(isset($standardized->hasOffenses)){
			$check->has_offense = $standardized->hasOffenses;
		    $check->has_sex_offense = $standardized->hasSexOffense;
		}
		
		//TODO: This doesn't seem to work
		if(isset($standardized->hasOffense)){
			$check->hasOffense = $standardized->hasOffense;
			$check->hasSexOffense = $standardized->hasSexOffense;
		}

		$check->is_completed();
		$check->save();
		
		if( isset(auth()->user) && auth()->user()->apiuser){
				
			Log::info("RETURNING NATIONAL TRI-EYE DATA TO CONTROLLER");
			
			if(isset($standardized->xml)){
				//Log::info(json_encode($standardized->xml));
				return $standardized->xml;
			}else{
				return $standardized;
			}
		}
		
		
		if( isset(auth()->user()->apiuser) ){
			
			Log::info("RETURNING NEW NATIONAL TRI-EYE DATA TO CONTROLLER");
			
			if(isset($standardized->xml)){
				//Log::info(json_encode($standardized->xml));
				return $standardized->xml;
			}else{
				return $standardized;
			}
		
		
    	}

    }
}
