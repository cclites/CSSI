<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Crypt;

use \App\Models\Mvr;
use \App\Models\Check;
use \App\Models\Checktype;
use \App\Models\Report;
use App\Models\Stat;

use Carbon\Carbon;
use Exception;
use Log;
use DB;


class SambaCheckOrder extends Command{
		
	protected $signature = 'samba_checks';
	protected $description = 'Check for available live MVRs from Samba';
	
	protected $stat;

    public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		
		$type = \App\Models\Type::where('id', 10)->first();
		
		if(!$type->enabled){
			Log::info("Check is not enabled. Unable to run MVR check order");
			return;	
		}
		
		$this->stat = Stat::where('name', 'MvrCheck')->first();
		
		checkConfigs();
		
		$stat = $this->stat;
		$stat->val = "Checking for Orders";
		$stat->save();

		$msg = "Checking for Live MVR Orders";
		cLog($msg, 'app/commands', 'mvr');
		
		$badString = '<?xml version="1.0"?>';
		$commsXML = simplexml_load_file(config_path("samba/samba-communication.xml"));
		
		$commsXML->Account = env("SAMBA_ACCOUNT");
		$commsXML->UserID = env("SAMBA_USER");
		$commsXML->Password = env("SAMBA_PASS");
		
		$transform = $commsXML->asXML();
		$transform = trim(str_replace($badString, "", $transform));
		$inCommunications = "<inCommunications>" . htmlentities($transform) . "</inCommunications>";
		
		include_once config_path("samba/samba-receive-envelope.php");
		$envelope = sambaReceiveEnvelope($inCommunications);

		$response = ReportController::sendReportOrderSoap($envelope, "ReceiveRecords", false);
		$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
		
		cLog("Check MVR Order response.\n$response", 'app/commands', 'mvr');

		if(empty($response)){
			Log::info("*****************************  MVR Live RESPONSE IS EMPTY ************************");
			$stat->val = "Ready";
		    $stat->save();
			return;
		}elseif( isset($xml->sBody->faultcode) ){
			Log::info("MVR Sandbox Live Receive Error: " . $xml->sBody->faultcode);
		}else{
			Log::info("***** Received Live MVR Response *******");
		}

		$xml = new \SimpleXMLElement($response);
		
		if(!isset($xml->sBody->ReceiveRecordsResponse->ReceiveRecordsResult)){
			
			$msg = json_encode($xml);
		    cLog($msg, 'app/commands', 'mvr');
			
			Log::info("MVR live check: No ReceiveRecordsResult");
			Log::info(json_encode($xml));
			
			$stat->val = "Ready";
		    $stat->save();
			
			return;
		}
		
		try{
			//error check here first?
			
					
			$daysLeft = $xml->sBody->ReceiveRecordsResponse->ReceiveRecordsResult->CallValidation->DaysLeft;
			Log::info("MVR Live Days left = $daysLeft");
			
			DB::table('stats')->where('name', 'MvrPasswordDaysRemaining')->update(['val'=>$daysLeft]);

			
		}catch(Exception $e){
			Log::info("Unable to retrieve days left. Dumping response: ");
			Log::info( $response );
			
			$stat->val = "Ready";
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
	