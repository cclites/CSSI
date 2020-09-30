<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

// Models
use \App\Models\User;
use \App\Models\Check;
use \App\Models\Checktype;
use \App\Models\Type;
use \App\Models\Report;
use \App\Models\Securitec;
use \App\Models\Stat;

use DB;
use Log;

use Carbon\Carbon;
use Exception;

use Notification;
use \App\Notifications\ReportReadyEmail;

class SecuritecCheckOrder extends Command{
	
    protected $signature = 'securitec_checks';
    protected $description = 'Query Securetec for pending reports.';
	
	protected $stat;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	 public function handle(){
	 	

		$this->stat = Stat::where('name', 'FederalCheckOrderStatus')->first();
		$this->checkReadyList();
	 		
		/*	 	
		$checks = Check::whereNull('transaction_id')
                    ->whereNull('completed_at')
                    ->with('types')
                    ->get();
		*/			
		
		
	 }
	 
	 private function checkReadyList(){
	 	
		$stat = $this->stat;
		$stat->val = "Checking for Orders";
		$stat->save();
		
		$payload["password"] = env("SECURITEC_PASS");
		$payload["username"] = env("SECURITEC_USER");	
        $payload = http_build_query($payload);
	
		$headers = [
			"Content-Type: application/x-www-form-urlencoded"
	    ];
		
		$response = ReportController::sendReportOrder(env('SECURITEC_CHECK_ORDER_URL'), $payload, $headers);
		
		if($response){
			$responseXML = simplexml_load_string($response);
			$reportIds = $responseXML->BackgroundReportPackage->ProviderReferenceId->IdValue;
			
			if(count($reportIds)){
				$this->getReports($reportIds);
			}else{
				$stat->val = "Ready";
	      		$stat->save();
			}
		}else{
	      $stat->val = "Ready";
	      $stat->save();
	    }
		
	 }

	 private function getReports($reportIds){
	 	
		Log::info("*****SecuritecCheckOrder::getReports()");
		
		$stat = $this->stat;
		$stat->val = "Retrieving Orders";
		$stat->save();
		
		$headers = [
			"Content-Type: application/x-www-form-urlencoded"
	    ];
	 	
		foreach($reportIds as $key=>$id){
					
			$payload = [
			
				"password" => env("SECURITEC_PASS"),
				"username" => env("SECURITEC_USER"),
				"order_id" => $id . ""
			];
			
			$payload = http_build_query($payload);
			
			$response = ReportController::sendReportOrder(env('SECURITEC_GET_ORDER_URL'), $payload, $headers);
			
			if(!$response){
				$stat->val = "Ready";
		        $stat->save();
				return;
			}else{
				Log::info("****  Retrieved a Securitec Order. *******");
				$stat->val = "Processing Orders";
		        $stat->save();
			}
			
			$results = Securitec::SecuritecAPIStandardize($response);			
			$tracking = $results->Screening->ClientReferenceId->IdValue;
			

			$tuples = explode("_", $tracking);
			$temp = strtoupper($tuples[0]);

			$report = Report::where("tracking", $tracking)->first();
			
			if($report){
				
				$check = Check::where("id", $report->check_id)->first();
				
				$checktype = Checktype::where('check_id', $check->id)
				             ->where("type_id", $report->check_type)
							 ->first();
							 
				//I need to find the correct _check, but no way to track which check is which --
				
				// - First, find the order
				$order = \App\_Models\Order::where('original_id', $report->check_id)->first();
				
				
				$nCheck = \App\_Models\Check::where('order_id', $order->id)->where("type_id", $report->check_type)->first();
				
				$report->report = encrypt(json_encode($results) );
				$report->save();
				
				$checktype->is_completed();
				$checktype->save();
				
				$check->is_completed();
				$check->save();
				
				try{
					
					$orders = \App\_Models\Order::where('original_id', $report->check_id)->get();
					
					Log::info("Retrieved Orders");
					
					foreach($orders as $order){
						
						$checks = $order->checks;
						
						foreach($checks as $ck){
									
							$ck->completed_at = Carbon::now();	
							$ck->save();
						}
						
					}
					
					Log::info("Processed orders");
					
				}catch(\Exception $e){
					Log::error("Unable to retrieve orders");
				}
				
				$check->user->notify(new ReportReadyEmail($check->id));
				$this->clearReport($id . "");
				
			}else{
				Log::info("No report was created for this Securitec check.");
				$stat->val = "Ready";
		        $stat->save();
			}

		}
				
	 }
	 
	 private function clearReport($id){
	 	
		Log::info("*****SecuritecCheckOrder::clearReports($id)");
		
		$stat = $this->stat;
		$stat->val = "Clearing Orders";
		$stat->save();
		
		$payload = [
			
				"password" => env("SECURITEC_PASS"),
				"username" => env("SECURITEC_USER"),
				"order_id" => $id . ""
			];

        $payload = http_build_query($payload);
	
		$headers = [
			"Content-Type: application/x-www-form-urlencoded"
	    ];
		
		$response = ReportController::sendReportOrder(env('SECURITEC_CLEAR_ORDER_URL'), $payload, $headers);
		
		$stat->val = "Ready";
		$stat->save();
		
	 }
	
	
}
