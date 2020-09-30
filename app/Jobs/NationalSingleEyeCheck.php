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

class NationalSingleEyeCheck implements ShouldQueue
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
		
		if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		}

        // load query XML template
        $queryXML = simplexml_load_file(config_path("data_divers/national-single-eye-request-template.xml"));
        
        // Update XML
        $queryXML->SubjectDetail->FirstName = $check->first_name;
        $queryXML->SubjectDetail->LastName = $check->last_name;
		

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
		 
        $queryXML->Authentication->Username = DataDiver::user();
        $queryXML->Authentication->Password = DataDiver::password();

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

        $response = curl_exec($ch); // perform the post
        curl_close($ch); // close the session

        $results = \App\Models\DataDiver::NationalSingleEyeApiStandardize($response);
		
		if(is_null($results)){
			return null;
		}else{
			
			cLog(json_encode($results), "app/jobs", 'national_single_eye');
			$report = new \App\Models\Report;
			
			$report->report = encrypt(json_encode($results));

            /*
			if(isset($results["xml"])){
				Log::info("Encoding as XML");
				$report->report = encrypt(json_encode($results["xml"]));
			}else{
				Log::info("Not encoding as XML");
				$report->report = encrypt(json_encode($results));
			}
			 * 
			 */
			
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 2;
			$report->save();
				
			Log::info("Add the check type for National Single Eye");
	        $checktype = $check->checktypes->where('type_id', 2)->first();
	        $checktype->is_completed();
			$checktype->save();
			
			if(isset($results->hasOffenses)){
				$check->has_offense = $results->hasOffenses;
				//$check->has_sex_offense = $results->hasSexOffense;
			}
				
			$check->is_completed();
			$check->save();
			
			return $results;
			
			/*
			if(auth()->user()->apiuser){
				if(isset($results["xml"])){
					return $results["xml"];
				}else{
					return $results;
				}
					
			}
			 * 
			 */
				
		}
       
    }
}
