<?php

namespace App\Jobs;

use App\Models\Securitec;
use App\Models\Check;
use App\Models\State;
use App\Http\Controllers\ReportController;
use \Carbon\Carbon;
use Log;
use Crypt;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CountyTriEyeCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	protected $state;


    public function __construct(Check $check, $state)
    {
        $this->check = $check;
		$this->state = $state;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$check = $this->check;
		$state = $this->state;

    	$stateObj = State::getStateByCode($state);
		$stateCode = $stateObj->code;
		
		$profile = new \stdClass();
		
		if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
			$dob = $profile->birthday->format('Y') . "-" . $profile->birthday->format('m') . "-" . $profile->birthday->format('d');
		}else{
			$dob = $this->check->birthday->format('Y') . "-" . $this->check->birthday->format('m') . "-" . $this->check->birthday->format('d');
		}

		foreach ($check->counties as $c) {

			//county_tri_eye_county_ids
			$queryXML = simplexml_load_file(config_path("securitec/create_county_check.xml"));
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->GivenName = $check->first_name;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->MiddleName = $check->middle_name;
	        $queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->FamilyName = $check->last_name;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->DemographicDetail->GovernmentId = $profile->ssn;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->DemographicDetail->DateOfBirth = $dob;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PostalAddress->Region = $stateCode;
			$queryXML->BackgroundSearchPackage->Screenings->Screening->Region = $stateCode;
			$queryXML->BackgroundSearchPackage->Screenings->Screening->SearchCriminal->County = $c->title;
			
			
			//Append the report tracking ID
			$trackingId = strtoupper($check->provider_reference_id) . ":" . $c->id . "_4";
			$queryXML->BackgroundSearchPackage->Screenings->Screening->ClientReferenceId->IdValue = $trackingId;
			
			$payload = array(
			               "xml" => $queryXML->asXML(),
			               "password" => Securitec::password(),
			  			   "username" => Securitec::user()
					   );
					   
			$payload = http_build_query($payload);
			
			$headers = [
				"Content-Type: application/x-www-form-urlencoded"
		    ];

            //Am sending a single request per order
			$response = ReportController::sendReportOrder(env('SECURITEC_SEND_ORDER_URL'), $payload, $headers);
			
			Log::info("Securitec Response:");
			Log::info(json_encode($response));

			$report = new \App\Models\Report;
			$report->report = encrypt(json_encode(["message"=>"Order is Pending", "status"=>0, "tracking"=>$trackingId]));
			$report->check_id = $check->id;
			$report->tracking = $trackingId;
			$report->check_type = 4;
			$report->county = $c->id;
			$report->save();

		}
        
    }
}
