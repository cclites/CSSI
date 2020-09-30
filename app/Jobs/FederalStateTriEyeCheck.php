<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Securitec;
use App\Http\Controllers\ReportController;
use Log;
use \Carbon\Carbon;
use Crypt;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FederalStateTriEyeCheck implements ShouldQueue
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
			$dob = $profile->birthday->format('Y') . "-" . $profile->birthday->format('m') . "-" . $profile->birthday->format('d');
		}else{
			$dob = $this->check->birthday->format('Y') . "-" . $this->check->birthday->format('m') . "-" . $this->check->birthday->format('d');
		}
		
		foreach($check->federal_states as $s){
			$queryXML = simplexml_load_file(config_path("securitec/create_order_federal_statewide_criminal.xml"));
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->GivenName = $check->first_name;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->MiddleName = $check->middle_name;
	        $queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PersonName->FamilyName = $check->last_name;
			//$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->DemographicDetail->GovernmentId = $profile->ssn();
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->DemographicDetail->GovernmentId = $profile->ssn;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->DemographicDetail->DateOfBirth = $dob;
			$queryXML->BackgroundSearchPackage->ScreeningsSummary->PersonalData->PostalAddress->Region = $s->code;
			$queryXML->BackgroundSearchPackage->Screenings->Screening->Region = $s->code;
			
			
			$trackingId = strtoupper($check->provider_reference_id) . ":" . $s->id . "_6";
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
	
			$response = ReportController::sendReportOrder(env('SECURITEC_SEND_ORDER_URL'), $payload, $headers);
			
			$report = new \App\Models\Report;
			$report->report = encrypt(json_encode(["message"=>"Order is Pending", "status"=>0, "tracking"=>$trackingId]));
			$report->check_id = $check->id;
			$report->tracking = $trackingId;
			$report->check_type = 6;
			$report->state = $s->id;
			$report->save();
	    }

    }
}
