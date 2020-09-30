<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\State;
use App\Models\Mvr;
use App\Models\Checktype;
use App\Http\Controllers\ReportController;

use \Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use DB;
use Response;
use Crypt;
use Auth;

class MvrCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	protected $mvr;

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

		Log::info("JOBS/MVRCHECK");


		$state = $check->states[0];
		$badString = '<?xml version="1.0"?>';
		
		if($state->id == 39 || $state->id == 45){
			return json_encode(["status" => 3, "message" => "MVR checks for Pennsylvania and Utah are unavailable", "tracking"=>""]);
		}
		
		/*******************************************************************/
		$commsXML = simplexml_load_file(config_path("samba/samba-communication.xml"));
		$commsXML->Account = Mvr::account();
		$commsXML->UserID = Mvr::user();
		$commsXML->Password = Mvr::password();
		/*******************************************************************/
	
	    /*******************************************************************/
		//remove the xml opening statement
		$transform = $commsXML->asXML();
		$transform = trim(str_replace($badString, "", $transform));		
		$inCommunications = "<inCommunication>" . htmlentities($transform) . "</inCommunication>";
        /*******************************************************************/
        
        $profile = new \stdClass();
        
        if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		}
		
		//Log::info("JOBS/PROFILE");
		//Log::info(json_encode($profile));

        
		/*******************************************************************/
		$orderXML = simplexml_load_file(config_path("samba/samba-order.xml"));
        $orderXML->FirstName = $check->first_name;
		$orderXML->MiddleName = $check->middle_name;
        $orderXML->LastName = $check->last_name;	
		$orderXML->DOB->Month = $profile->birthday->format('m');
        $orderXML->DOB->Day = $profile->birthday->format('d');
        $orderXML->DOB->Year = $profile->birthday->format('Y');
		$orderXML->License = $profile->license_number;
		$orderXML->State->Abbrev = $state->code;
		$orderXML->State->Full = $state->title;
		$orderXML->AuxMisc = strtoupper($check->provider_reference_id);
		
		$orderXML->OriginationID = $check->company_id;
		

		$overnight = array(2, 12, 26);
		if(in_array($state->id, $overnight)){
	    	$orderXML->Handling = "AO"; 
		}
		if($state->id === 26){
			$orderXML->Subtype = "DB"; 
		}
		
		//remove the xml opening statement
		$transform = $orderXML->asXML();
		$transform = trim(str_replace($badString, "", $transform));
		$inOrder = htmlentities($transform);
		/*******************************************************************/

		include_once config_path("samba/samba-envelope.php");
		
		/*
		if(Auth::user()->apiuser){
		    include_once config_path("samba/samba-in-order-wrapper.php");
		    $inOrder = orderWrapper($inOrder);
			$action = 'OrderInteractive';
			$envelope = sambaInteractiveEnvelope($action, $inCommunications, $inOrder);
		}else{*/
			include_once config_path("samba/samba-order-wrapper.php");
		    $inOrder = orderWrapper($inOrder);
			$action = "SendOrders";
			$envelope = sambaEnvelope($action, $inCommunications, $inOrder);
		//}
		
		//Log::info($envelope);
		
		//$envelope = sambaEnvelope($action, $inCommunications, $inOrder);
		//$envelope = sambaEnvelope('SendOrders', $inCommunications, $inOrder);
		
		
		//update to this when adding Neeyamo checks
		
		//$wsdl = !$sandbox ? env("SAMBA_WSDL") : env("SAMBA_WSDL_SANDBOX");
		//$actionUrl = "http://adrconnect.mvrs.com/adrconnect/2013/04/IAdrConnectWebService/";
		//$response = ReportController::sendReportOrderSoap($envelope, "SendOrders", $wsdl, $actionUrl);

		//$response = ReportController::sendReportOrderSoap($envelope, "SendOrders", auth()->user()->sandbox);
		$response = ReportController::sendReportOrderSoap($envelope, $action, auth()->user()->sandbox);
		
		Log::info("Response to check request ------------------------------");
		Log::Info(json_encode($response));

		if($response == ""){
			Log::info("No response after sending order");
			return null;
		}else{
			//make sure we have a valid request
			$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
			$xml = new \SimpleXMLElement($response);
			$body = $xml->xpath('//sBody')[0];
			$array = json_decode(json_encode((array)$body), TRUE);
			
			//Log::info(json_encode($xml));
			
			$report = new \App\Models\Report;
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 10;
			$report->state = $state->id;
			$report->save();
			
			
			if(!isset($array["SendOrdersResponse"]) && !isset($array["DlRecord"])){
				$report->report = encrypt(json_encode($array));
				$report->save();
				return;
			}
			
			
			
			//if(isset($array["SendOrdersResponse"]) && $array["SendOrdersResponse"]["SendOrdersResult"]["CallValidation"]["ErrorId"] != 0){
			if( isset($array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorId"]) &&
		        $array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorId"] != 0 ){
				
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0');
				$check->active = false;
				$check->transaction_id = "11111111";
				$check->save();
				
				//delete the check_type also.
				//DB::table('check_type')->where('check_id', $check->id)->delete();
				
				DB::statement('SET FOREIGN_KEY_CHECKS=1');
				
				$message = $array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorDescription"];
				
				$report->report = encrypt( json_encode(["message"=>"Order is Pending ", "status"=>-1, "tracking"=>$message]) );
				$report->save();
				
				return ['error' => "There was system failure with this request."];
				
			}		
			
			
			$report->report = encrypt(json_encode(["message"=>"Order is Pending ", "status"=>0, "tracking"=>strtoupper($check->provider_reference_id)]));
			$report->save();
				
			/*	
			}elseif( isset($array["DlRecord"]) && $array["DlRecord"]["Result"]["ErrorCode"] != 0){
				$msg =  $array["DlRecord"]["Result"]["ErrorDescription"];
				$msg .= " This check will not be billed.";
				$report->report = encrypt(json_encode($msg));
				$report->save();
				
				//this should prevent the check from being billed.
				$check->transaction_id = "000000";
				$check->save();
			}else{
				$report->report = encrypt(json_encode(["message"=>"Order is Pending", "status"=>0, "tracking"=>strtoupper($check->provider_reference_id)]));
				$report->save();
			}
			*/
			
			
			

		}

		return 0;	
		
    }
 
}

