<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Neeyamo;
use App\Models\Education;

use \Carbon\Carbon;

// Notifications
use Notification;
use \App\Notifications\NewEducationCheckEmail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Log;
use Crypt;

class EducationCheck implements ShouldQueue
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
    	Log::info("In Neeyamo education check");
		
        $check = $this->check;
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		
		//Log::info(json_encode($profile));
		
		//Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new NewEducationCheckEmail($check, $profile));
		//return;

		if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
			$dob = $profile->birthday->format('Y') . "-" . $profile->birthday->format('m') . "-" . $profile->birthday->format('d');
		}
		
		

		$xml = simplexml_load_file(config_path("neeyamo/education.xml"));
		
		$xml->VerificationHeader->TransactionType=1; //1 is for ordering the report
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
		
		if(isset($profile->college_name)){
			
			$xml->VerificationDetail->University = $profile->college_name;
			$xml->VerificationDetail->YearOfPassing = $profile->college_graduation_year;
			$xml->VerificationDetail->Degree = $profile->college_degree_type;
			$xml->VerificationDetail->City = $profile->college_city;
			$xml->VerificationDetail->State = $profile->college_state;
			$xml->VerificationDetail->ZipCode = $profile->college_zip;
			$xml->VerificationDetail->Graduated = $profile->college_is_graduated;
			
			if(isset($profile->college_phone)){
				$xml->VerificationDetail->AdditionalInformation = "Phone: " . $profile->college_phone;
			}
			
			$this->submit($xml, $check);

		}
		
		if(isset($profile->high_school_name)){
			
			$xml->VerificationDetail->University = $profile->high_school_name;
			$xml->VerificationDetail->YearOfPassing = $profile->high_school_graduation_year;
			$xml->VerificationDetail->Degree = $profile->high_school_degree_type;
			$xml->VerificationDetail->City = $profile->high_school_city;
			$xml->VerificationDetail->State = $profile->high_school_state;
			$xml->VerificationDetail->ZipCode = $profile->high_school_zip;
			$xml->VerificationDetail->Graduated = $profile->high_school_is_graduated;
			
			if(isset($profile->high_school_phone)){
				$xml->VerificationDetail->AdditionalInformation = "Phone: " . $profile->high_school_phone;
			}
			
			$this->submit($xml, $check);
		}
	

		$this->createEducation($check, $profile);
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
			
			Log::info("Education submission RESPONSE:");
			Log::info(json_encode($response));
			
			$xml = simplexml_load_string($response->PlaceMultiChecksResult);
			
			//{"PlaceMultiChecksResult":"<xml><order orderID=\"ESSIUS-0918-00001\"><\/order><\/xml>"}

			//Create a report
			$report = new \App\Models\Report;
			$report->check_id = $check->id;
			$report->provider_id = $xml->order["orderID"];
			$report->report = encrypt(json_encode(["status"=>"You will be emailed when your Education validation is ready"]));
			$report->check_type = 9;
			$report->save();
			
			//verify report exists

		} 
		catch (\Exception $e) 
		{ 
		    Log::info("<h2>Exception Error!</h2>"); 
		    Log::info($e->getMessage()); 
		}
		
		return;
		
    }

	public function createEducation($check, $profile){

		$education = new Education;
        $education->check_id = $check->id;
        $education->college_name = $profile->college_name;
        $education->college_city_and_state = $profile->college_city_and_state;
        $education->college_phone = $profile->college_phone;
        $education->college_years_attended = $profile->college_years_attended;
        $education->college_is_graduated = $profile->college_is_graduated;
        $education->college_degree_type = $profile->college_degree_type;
        $education->high_school_name = $profile->high_school_name;
        $education->high_school_city_and_state = $profile->high_school_city_and_state;
        $education->high_school_phone = $profile->high_school_phone;
        $education->high_school_years_attended = $profile->high_school_years_attended;
        $education->high_school_is_graduated = $profile->high_school_is_graduated;
        $education->high_school_degree_type = $profile->high_school_degree_type;
        $education->save();
		
		
		Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new NewEducationCheckEmail($check, $profile));
		return;
		
	}

}
