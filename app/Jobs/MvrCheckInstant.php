<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\State;
use App\Models\Mvr;
use App\Models\Checktype;
use App\Models\Transaction;

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


class MvrCheckInstant implements ShouldQueue
//class MvrCheckInstant implements ShouldQueue
{
    //use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    	Log::info("JOBS/MVRCHECKINSTANT::handle");
		
    	$check = $this->check;
		$state = $check->states[0];
		
		//Create new report
		$report = new \App\Models\Report;
		$report->check_id = $check->id;
		$report->tracking = strtoupper($check->provider_reference_id);
		$report->check_type = 10;
		$report->state = $state->id;
		
		
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
		Log::info("COMMS XML");
		Log::info(json_encode($commsXML));
		
		
	    /*******************************************************************/
		//remove the xml opening statement
		$transform = $commsXML->asXML();
		$transform = trim(str_replace($badString, "", $transform));		
		$inCommunications = "<inCommunications>" . htmlentities($transform) . "</inCommunications>";
        /*******************************************************************/
        
        $profile = new \stdClass();
        
        if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
			$bday = explode("-", $profile->birthday);
			$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		}

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
		include_once config_path("samba/samba-in-order-wrapper.php");
		

	    $inOrder = inOrderWrapper($inOrder);
		$action = 'OrderInteractive';
		$envelope = sambaInteractiveEnvelope($action, $inCommunications, $inOrder);
		
		$response = ReportController::sendReportOrderSoap($envelope, $action, auth()->user()->sandbox);
			  
		Log::info("Response to check request ------------------------------");
		Log::Info(json_encode($response));

		if($response == ""){
			
			Log::info("No response after sending order");
			$description = "MVR Request for " . $check->first_name . " " . $check->last_name . " has encountered a system error.";
			
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0');
			
			//Generate a transaction - 
			$transaction = new Transaction;
			$transaction->amount = 0;
			$transaction->check_id = $check->id;
			$transaction->parent_id = $check->user->company_id;
			$transaction->user_id = $check->user_id;
			$transaction->description = $description;
			$transaction->notes = 'error';
			$transaction->save();
			
			
			DB::table('check_type')
            	->where('check_id', $check->id)
            	->update(['completed_at'=>Carbon::now()]);
			
			DB::table('checks')
            	  ->where('id', $check->id)
            	  ->update(['completed_at'=>Carbon::now(), 'transaction_id' => $transaction->id]);
            
            
            $report->report = encrypt($description);
			$report->save();
			
            /*	  
            DB::table('report')
            	  ->where('check_id', $check->id)
            	  ->update([ 'report'=> encrypt($description)]);*/
			
			return ['error' => "System Error", 'description' => $description, 'status' => -1];

		}
			
		//make sure we have a valid request
		$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
		$xml = new \SimpleXMLElement($response);
		$body = $xml->xpath('//sBody')[0];
		$array = json_decode(json_encode((array)$body), TRUE);
		
		//Log::info(json_encode($xml));
		
		//$report = new \App\Models\Report;
		//$report->check_id = $check->id;
		//$report->tracking = strtoupper($check->provider_reference_id);
		//$report->check_type = 10;
		//$report->state = $state->id;
		
		Log::info("Report has been created");
		
		//Check for configuration errors in request, such as bad password, etc.
		//These types of errors should never get charged.
		if( isset($array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorId"]) &&
		    $array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorId"] != 0
		 ){
		   	Log::info("I have an error. ErrorId is " . $array["OrderInteractiveResponse"]["OrderInteractiveResult"]["CallValidation"]["ErrorId"]);
			$description = "MVR Request for " . $check->first_name . " " . $check->last_name . " has encountered a system error.";
		   	
		   	DB::statement('SET FOREIGN_KEY_CHECKS=0');
			
			//Generate a transaction - 
			$transaction = new Transaction;
			$transaction->amount = 0;
			$transaction->check_id = $check->id;
			$transaction->parent_id = $check->user->company_id;
			$transaction->user_id = $check->user_id;
			$transaction->description = $description;
			$transaction->notes = 'error';
			$transaction->save();
			
			DB::table('check_type')
            	->where('check_id', $check->id)
            	->update(['completed_at'=>Carbon::now()]);
			
			DB::table('checks')
            	  ->where('id', $check->id)
            	  ->update(['completed_at'=>Carbon::now(), 'transaction_id' => $transaction->id]);
            	  
            $report->report = encrypt($description);
			$report->save();
			
			Log::info("****MVR request system error");
			return ['error' => $description, 'description' => $description, 'status' => -1];
		 }
				
			
		if(!$array["OrderInteractiveResponse"]){
			return ['error' => 'No order was created', 'description' => "Unknown system failure with this request.", 'status'=>-1];
		}
		
		$data = $array["OrderInteractiveResponse"]["OrderInteractiveResult"]["Report"];
		$standardizedResults = Mvr::MvrInstantAPIStandardize($data);
		
		if(!$standardizedResults){
			Log::info("****MVR request file parse error");
			return ['error' => 'Unrecognized output error', 'description' => "Unrecognized file format", 'status'=>-1];
		}
		
		if($standardizedResults->DlRecord->Result->Valid == "N"){
			
			Log::info("****MVR DLRecord is not valid.");
			Log::info("I have an error: " . $standardizedResults->DlRecord->Result->ErrorDescription);
			
			$errorMessage = $standardizedResults->DlRecord->Result->ErrorDescription;
			
			$description = "MVR Request for " . $check->first_name . " " . $check->last_name . " has failed - $errorMessage";
			
			if($errorMessage == "SYSTEM IS DOWN"){
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0');
				
				//Generate a transaction - 
				$transaction = new Transaction;
				$transaction->amount = 0;
				$transaction->check_id = $check->id;
				$transaction->parent_id = $check->user->company_id;
				$transaction->user_id = $check->user_id;
				$transaction->description = $description;
				$transaction->notes = 'ERROR';
				$transaction->save();
				
				DB::table('check_type')
            	->where('check_id', $check->id)
            	->update(['completed_at'=>Carbon::now()]);
			
				DB::table('checks')
	            	  ->where('id', $check->id)
	            	  ->update(['completed_at'=>Carbon::now(), 'transaction_id' => $transaction->id]);
	            	  
	            DB::table('report')
	            	  ->where('check_id', $check->id)
	            	  ->update([ 'report'=> encrypt($description)]);
					
				DB::statement('SET FOREIGN_KEY_CHECKS=1');
				
				$standardizedResults = ['error'=>$standardizedResults];
				
				$report->report = encrypt($standardizedResults);
			    $report->save();
				
				return ['error' =>$standardizedResults , 'description' => $description, 'status' => 0];

			}else{
				
				$message = $standardizedResults;
			}

			
		}else{
			$message = $standardizedResults;
		}

		$report->report = encrypt(json_encode($message));
		$report->save();
		
		$checktype = Checktype::where('check_id', $report->check_id)
								->where('type_id', 10)->first();

		$checktype->is_completed();
		$checktype->save();
		
		$check->is_completed();
		$check->save();
			
		//standardized results gets returned no matter what.
		return $standardizedResults;
		
    }
 
}

