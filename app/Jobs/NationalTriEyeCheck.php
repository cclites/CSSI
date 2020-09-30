<?php

namespace App\Jobs;

use App\Models\DataDiver;
use App\Models\Check;
use App\Models\Report;
use App\Http\Controllers\ReportController;
use \Carbon\Carbon;
use Log;
use Crypt;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NationalTriEyeCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;

    public function __construct(Check $check)
    {
        $this->check = $check;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $check = $this->check;
		$profile = new \stdClass();
        
        // load query XML template
        $queryXML = simplexml_load_file(config_path("data_divers/national-tri-eye-request-template.xml"));
		
        // Update XML
        $queryXML->SubjectDetail->FirstName = $check->first_name;
        $queryXML->SubjectDetail->LastName = $check->last_name;
		
		if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		}
			 
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

        // update
        $queryXML->Authentication->Username = DataDiver::user();
        $queryXML->Authentication->Password = DataDiver::password();
		
		$queryXML->ServiceRequest->ClientOrderNumber = $check->provider_reference_id;

        // save the updated document
        $request = array('query' => $queryXML->asXML());
        $payload = http_build_query($request);
		
		//$s = print_r($payload, true);
		//Log::info($s);
		
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

        $response = curl_exec($ch); // perform the post
        curl_close($ch); // close the session
        
        $standardized = \App\Models\DataDiver::NationalTriEyeApiStandardize($response);

		if(is_null($standardized)){
			return null;
		}else{
			
			cLog(json_encode($standardized), "app/jobs", 'national_tri_eye');
			
			$report = new \App\Models\Report;
			
			//$100 says the first condition is never met. That's why everything is being stored in the 
			//report.
			if(isset($standardized->xml)){
				Log::info("NationalTriEye: I have an XML object");
				$report->report = encrypt(json_encode($standardized->xml));
			}else{
				Log::info("NationalTriEye: I do not have an XML object");
				$report->report = encrypt(json_encode($standardized));
			}
			
			echo "Setting the report details\n";	
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 1;
			$report->save();
			
			echo "completing the checktypes\n";
	        $checktype = $check->checktypes->where('type_id', 1)->first();
	        $checktype->is_completed();
			$checktype->save();
			
			
			echo "Saved Checktypes\n";
			
			if(isset($standardized->hasOffenses)){
				$check->has_offense = $standardized->hasOffenses;
			    $check->has_sex_offense = $standardized->hasSexOffense;
			}
			
			echo "After checking offenses.\n";

			$check->is_completed();
			$check->save();
			
			echo "After completing check\n";
			
			if(auth()->user()->apiuser){
				
				Log::info("RETURNING NATIONAL TRI-EYE DATA TO CONTROLLER");
				
				if(isset($standardized->xml)){
					Log::info(json_encode($standardized->xml));
					return $standardized->xml;
				}else{
					return $standardized;
				}

				
				
			}
				
		}

		
    }
}
