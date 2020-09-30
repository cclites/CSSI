<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Neeyamo;
use App\Models\Employment;

use \Carbon\Carbon;

// Notifications
use Notification;
use \App\Notifications\NewEmploymentHistoryCheckEmail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use DB;
use Crypt;
use Log;

class EmploymentCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;

    public function __construct(Check $check)
    {
        $this->check = $check;
    }

 
    public function handle()
    {
    	Log::info("Handling the employment check");
		
        $check = $this->check;
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		
		//$s = print_r($profile, true);
		//Log::info($s);
		
		$xml = simplexml_load_file(config_path("neeyamo/employment.xml"));
		
		Log::info(json_encode($xml));
		
		$xml->VerificationHeader->TransactionType=1; //1 is for ordering
		$xml->VerificationHeader->Password = env("NEEYAMO_PASS");
		$xml->VerificationHeader->UserName = env("NEEYAMO_USER");
		$xml->VerificationHeader->ClientID = env("NEEYAMO_CLIENT_ID");
		$xml->VerificationHeader->ClientCode = env("NEEYAMO_CLIENT_CODE");
		$xml->VerificationHeader->VerificationRequestID = strtoupper($check->provider_reference_id);
		
		$xml->OrderBasicDetail->InternalReferenceID = strtoupper($check->provider_reference_id);
		$xml->OrderBasicDetail->ApplicantFirstName = $check->first_name;
		$xml->OrderBasicDetail->ApplicantMiddleName = $check->middle_name;
		$xml->OrderBasicDetail->ApplicantLastName = $check->last_name;
		$xml->OrderBasicDetail->SSN = $profile->ssn;
		
		//if( $profile->current_employer_name ){
		if( isset($profile->current_employer_name) ){
			$xml->VerificationDetail->isFirstJob = 1;
			$xml->VerificationDetail->EmployerName = $profile->current_employer_name;
			$xml->VerificationDetail->EmploymentDurationFrom = $profile->current_hire_date;
			$xml->VerificationDetail->Designation = $profile->current_job_title;
			$xml->VerificationDetail->Address = $profile->current_employer_address;
			$xml->VerificationDetail->City = $profile->current_employer_city;
			$xml->VerificationDetail->State = $profile->current_employer_state;
			$xml->VerificationDetail->Zip = $profile->current_employer_zip;
			$xml->VerificationDetail->IsFirstJob = 0;
			
			if(isset($profile->current_employer_phone)){
				$xml->VerificationDetail->AdditionalInformation = "Phone: " . $profile->current_employer_phone;
			}
			
			$this->submit($xml, $check);
		}
		//else if( $profile->past_employer_name ){
		if( isset($profile->past_employer_name) ){
			$xml->VerificationDetail->isFirstJob = 0;
			$xml->VerificationDetail->EmployerName = $profile->past_employer_name;
			$xml->VerificationDetail->EmploymentDurationFrom = $profile->past_hire_date;
			$xml->VerificationDetail->EmploymentDurationTo = $profile->past_end_date;
			$xml->VerificationDetail->Designation = $profile->past_job_title;
			$xml->VerificationDetail->Address = $profile->past_employer_address;
			$xml->VerificationDetail->City = $profile->past_employer_city;
			$xml->VerificationDetail->State = $profile->past_employer_state;
			$xml->VerificationDetail->Zip = $profile->past_employer_zip;
			$xml->VerificationDetail->IsFirstJob = 0;
			
			if(isset($profile->past_employer_phone)){
				$xml->VerificationDetail->AdditionalInformation = "Phone: " . $profile->past_employer_phone;
			}
			
			$this->submit($xml, $check);

		}
		
		$this->createEmployment($check, $profile);
		return;
		
		
    }

	public function submit($xml, $check){
    	
		//return;
		$commsXML = $xml->asXML();
 
        $wsdl   = "https://usverificationclientuat.neeyamo.com/ibgvwcf/NeeyamoService.svc?singleWsdl";
		$client = new \SoapClient($wsdl, array('trace'=>1));  // The trace param will show you errors stack
		
		$request_param = array(
		    //"strOrderDetail" => $xml,
		    "strOrderDetail" => $commsXML,
		);
    	
		try
		{
		    $response = $client->PlaceMultiChecks($request_param);
			
			Log::info("EMPLOYMENT RESPONSE:");
			Log::info(json_encode($response));
			
			$xml = simplexml_load_string($response->PlaceMultiChecksResult);

			//Create a report
			$report = new \App\Models\Report;
			$report->check_id = $check->id;
			$report->provider_id = $xml->order["orderID"];
			$report->report = encrypt(json_encode(["status"=>"You will be emailed when your Employment validation is ready"]));
			$report->check_type = 8;
			$report->save();

		} 
		catch (\Exception $e) 
		{ 
		    Log::info("<h2>Exception Error!</h2>"); 
		    Log::info($e->getMessage()); 
		}
		
		return;
		
    }

    public function createEmployment($check, $profile){
    	
		$employment = new Employment;
        $employment->check_id = $check->id;
        $employment->current_employer_name = $profile->current_employer_name;
        $employment->current_employer_address = $profile->current_employer_address;
        $employment->current_employer_phone = $profile->current_employer_phone;
        $employment->current_job_title = $profile->current_job_title;
        $employment->current_hire_date = $profile->current_hire_date;
        $employment->past_employer_name = $profile->past_employer_name;
        $employment->past_employer_address = $profile->past_employer_address;
        $employment->past_employer_phone = $profile->past_employer_phone;
        $employment->past_job_title = $profile->past_job_title;
        $employment->past_hire_date = $profile->past_hire_date;
        $employment->save();
            
		Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new NewEmploymentHistoryCheckEmail($check, $profile));
		return;
    }
}
