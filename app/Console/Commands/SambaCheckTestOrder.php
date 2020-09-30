<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Crypt;

use \App\Models\Mvr;
use \App\Models\Check;
use \App\Models\Checktype;
use \App\Models\Report;
use \App\Models\Stat;


use Carbon\Carbon;
use Exception;
use DB;
use Log;


class SambaCheckTestOrder extends Command{
		
	protected $signature = 'samba_test_checks';
	protected $description = 'Check for available test MVRs from Samba';
	
	protected $stat;

    public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){//make sure that I have the most recent credentials
	
		$type = \App\Models\Type::where('id', 10)->first();
		
		if(!$type->enabled){
			Log::info("Check is not enabled. Not running MVR Check Order.");
			return;	
		}
	
	    $this->stat = Stat::where('name', 'MvrTestCheck')->first();
		
		checkConfigs();
		//$this->updatePassword();
		
		$stat = $this->stat;
		$stat->val = "Checking for Test Orders";
		$stat->save();
		
		//Log::info("Checking for Test Samba Orders");
		$msg = "Checking for Test Samba Orders";
		cLog($msg, 'app/commands', 'mvr_test');
		
		$badString = '<?xml version="1.0"?>';
		$commsXML = simplexml_load_file(config_path("samba/samba-communication.xml"));
		
		$commsXML->Account = env("SAMBA_ACCOUNT_SANDBOX");
		$commsXML->UserID = env("SAMBA_USER_SANDBOX");
		$commsXML->Password = env("SAMBA_PASS_SANDBOX");
		
		$transform = $commsXML->asXML();
		$transform = trim(str_replace($badString, "", $transform));
		$inCommunications = "<inCommunications>" . htmlentities($transform) . "</inCommunications>";
		
		include_once config_path("samba/samba-receive-envelope.php");
		$envelope = sambaReceiveEnvelope($inCommunications);
		
		$response = ReportController::sendReportOrderSoap($envelope, "ReceiveRecords", true);
		$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

		if(empty($response)){
			//Log::info("*****************************  SAMBA RESPONSE IS EMPTY ************************");
			$stat->val = "Ready";
		    $stat->save();
			return;
		}else{
			//Log::info("*************************** RECEIVED SAMBA RESPONSE *******");
			
			$msg = json_encode($response);
		    cLog($msg, 'app/commands', 'mvr_test');
			
		}
		
		if(empty($response)){
			Log::info("*****************************  SAMBA SANDBOX RESPONSE IS EMPTY ************************");
			$stat->val = "Ready";
		    $stat->save();
			return;
		}elseif( isset($xml->sBody->faultcode) ){
			Log::info("MVR Sandbox Check Receive Error: " . $xml->sBody->faultcode);
		}else{
			Log::info("***** Received Sandbox Samba Response *******");
			//Log::info(json_encode($response));
		}

		$xml = new \SimpleXMLElement($response);
		
		if(!isset($xml->sBody->ReceiveRecordsResponse->ReceiveRecordsResult)){
			
			$msg = json_encode($xml);
		    cLog($msg, 'app/commands', 'mvr_test');
			
			//Log::info("MVR sandbox check: No ReceiveRecordsResult");
			//Log::info(json_encode($xml));
			
			$stat->val = "Ready";
		    $stat->save();
			
			return;
		}
		
		try{
					
			$daysLeft = $xml->sBody->ReceiveRecordsResponse->ReceiveRecordsResult->CallValidation->DaysLeft;
			//Log::info("MVR Sandbox Days left = $daysLeft");
			
			//update the stat with the days left
			DB::table('stats')->where('name', 'MvrTestPasswordDaysRemaining')->update(['val'=>$daysLeft]);
			
		}catch(Exception $e){
			Log::info("Unable to retrieve days left. Dumping response: ");
			Log::info( $response );
			$stat->val = "Error: Unable to retrieve days remaining.";
		    $stat->save();
			return;
		}
		
		
		

		foreach($xml->sBody->ReceiveRecordsResponse->ReceiveRecordsResult->Reports->RecordEntity as $report){
			
			if(empty($report)){
				$stat->val = "Ready";
		        $stat->save();
				return;
			}else{
				
				//Log::info("\n*******************************************************************");
				//Log::info(json_encode($report));
			}
			
			$stat->val = "Saving the record";
		    $stat->save();
					
			$standardizedResults = Mvr::MvrAPIStandardize($report);
			$tracking = $standardizedResults->DlRecord->Criteria->AuxilliaryReference;
			
			$report = Report::where("tracking",$tracking)->where('check_type', 10)->first();
			$report->report = encrypt(json_encode($standardizedResults));
			$report->save();

			$checktype = Checktype::where('check_id', $report->check_id)
									->where('type_id', 10)->first();
														
			$checktype->is_completed();
			$checktype->save();
			
			//This assumes there is only one open check type
			$check = Check::where("id", $report->check_id)->first();
			$check->is_completed();
			$check->save();
			
		}
		
		$stat->val = "Ready";
		$stat->save();
		
	}

	
}
	