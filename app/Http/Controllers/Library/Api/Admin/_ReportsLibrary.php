<?php

namespace App\Http\Controllers\Library\Api\Admin;

//system
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

use App\_Models\Check;
use App\_Models\Invoice;
use App\_Models\Order;
use App\_Models\Company;

//facades
use Log;
use Auth;
use DB;
use Carbon\Carbon;


class _ReportsLibrary{
	
	protected $stateObjs;
	protected $typeObjs;
	protected $countyObjs;
	protected $districtObjs;
	protected $typesMap;
	protected $userPrices;
	protected $reportType;
	protected $cssiData = [11,12,13];

	
	public function __construct()
    {
		//require_once('../vendor/autoload.php');
		$this->stateObjs = cache('states');
		$this->countyObjs = cache('counties');
		$this->districtObjs = cache('districts');
		$this->typeObjs = cache('types');
		$this->typesMap = [];
		
    }
	
	public function createTransactionsReport($request){
		
        Log::info("Library/Api/Admin/_ReportsLibrary::createTransactionReport");
		
		$reportType = $request["report_type"];
		
		Log::info("Report type is $reportType");
		
		if($reportType == 'invoice_export'){

			$start = Carbon::today()->subMonth()->startOfMonth()->startOfDay();
			$end = Carbon::today()->subMonth()->endOfMonth()->endOfDay();
			
			//Start Date = 2019-03-01 05:00:00  
            //End Date = 2019-04-01 03:59:59  
            
			
			//$start = Carbon::today()->startOfDay()->startOfMonth();
			//$end = Carbon::today()->endOfDay()->endOfMonth();

		}elseif($reportType == 'adminDashboard' || $reportType == 'dailies'){
			
			$start = Carbon::today()->startOfDay();
			$end = Carbon::today()->endOfDay();
			
		}elseif($reportType == 'custom'){
			
			$start = Carbon::today()->startOfMonth()->startOfDay();
			$end = Carbon::today()->endOfMonth()->endOfDay();
			
			/*
			$start = $request["start"];
			$start = explode("-", $start);
			
			$end = $request["end"];
			$end = explode("-", $end);
			
			$start = Carbon::create($start[0], $start[1], $start[2], 0, 0, 0, "UTC");
			$end = Carbon::create($end[0], $end[1], $end[2], 0, 0, 0, "UTC");
			 * 
			 */
			
							
		}else{
			
			return json_encode(["error"=>"Not a valid report type"]);
			
		}
		
		$startDate = $start->setTimezone('UTC');
		$endDate = $end->setTimezone('UTC');
		
		Log::info("Start date is $startDate");
		Log::info("End date is $endDate"); 
				 
		$orders = Order::where('created_at', ">=", $startDate)
		                 ->where('created_at', "<=", $endDate)
						 ->where('sandbox', false)
						 ->get();
						 
		Log::info("Order count is " . $orders->count());
		
		if($orders->count() < 1){
			Log::info("No orders.");
		}

		foreach($orders as $order){
			
			$company = Company::where('company_id', $order->company_id)->first();
			
			if($company){
				
				$this->mapChecks($order, $company);
				
			}else{
				//die("Company does not exist --");
				Log::info("Company does not exist");
				Log::info("ORPHANED ORDER??");
				Log::info(json_encode($order));
				continue;
			}
			
		}
		
		Log::info("Add transactions to report.");
		
		$report = $this->addTransactionsToReport($reportType, $startDate, $endDate);
		
		//Log::info(gettype($report));
		//Log::info(json_encode($report));
		
		return $report;

	}

    public function mapChecks($order, $company){
    	
		//Log::info("Library/Api/Admin/_ReportsLibrary::mapChecks");
    	
		$checks = Check::where('order_id', $order->id)
				  ->whereNotIn('type', $this->cssiData)
		          ->get();
		
		$adjustment = 0;
		$minimum = 0;
				  
		if($order->invoice_id){
			$adjustment = Invoice::where("id", $order->invoice_id)->pluck("adjustment")->first();
		}
			
		
		//if($adj){
			//$adjustment = $adj;
		//}
		 
		//$adjustment = $order->adjustment;
		
		foreach($checks as $check){
			
			$typeId = $check->type;
			$amount = $check->amount;

			$typeObj = $this->typeObjs->where('id', $typeId)->first();
			$title = $baseTitle = $typeObj->title;
			$invoiceId = $order->invoice_id;

			if($typeId == 3){
				$data = json_decode($check->json_data);
				$stateId = $data->id;
				$state = $this->stateObjs->where('id', $stateId)->first();
				$baseTitle .= " (" . $state->code . ")";
			}elseif($typeId == 4){ //county
				$data = json_decode($check->json_data);
				$countyId = $data->id;
				$county = $this->countyObjs->where('id', $countyId)->first();
				$baseTitle .= " (" . $county->title . ":" . $county->state_code . ")";
			}elseif($typeId == 6){
				$data = json_decode($check->json_data);
				$stateId = $data->id;
				$state = $this->stateObjs->where('id', $stateId)->first();
				$baseTitle .= " (" . $state->code . ")";
			}elseif($typeId == 7){
				$data = json_decode($check->json_data);
				$districtId = $data->id;
				$district = $this->districtObjs->where('id', $districtId)->first();
				$baseTitle .= " (" . $districtObj->state_code . " ," . $districtObj->title  . ")";
			}elseif($typeId == 10){
				if($check->json_data){
					$data = json_decode($check->json_data);
					$stateId = $data->id;
					$state = $this->stateObjs->where('id', $stateId)->first();
					$baseTitle .= " (" . $state->code . ")";
				}else{
					$baseTitle .= " (n/a)";
				}
			}
			

			$this->addToTypesMap($typeId, $baseTitle, $invoiceId, $amount, $company, $adjustment, $minimum);
		}

        //Log::info("TYPES MAP --------");
		//Log::info($this->typesMap);
		//die();

    }
	
	public function addToTypesMap($typeId, $title, $invoiceId, $amount, $company, $adjustment = 0, $minimum = 0){
		
		//Log::info("Library/Api/Admin/_ReportsLibrary::addToTypesMap");
		
		
		
		$typesMap = $this->typesMap;
		$companyName = $company->company_name;
		
		if(in_array($typeId, $this->cssiData)){
			Log::info("Type id is not in CSSI Data");
			return;
		}
						
		if(isset($typesMap[$companyName][$title][$amount]["count"])){
			$typesMap[$companyName][$title][$amount]["count"] += 1;
		}else{
			$typesMap[$companyName][$title][$amount]["count"] = 1;
			$typesMap[$companyName][$title][$amount]["description"] = $title;
			$typesMap[$companyName][$title][$amount]["typeId"] = $typeId;
			$typesMap[$companyName][$title][$amount]["transactionId"] = 0;
			$typesMap[$companyName][$title][$amount]["company"] = json_encode($company);
			
			if( $invoiceId ){
				$typesMap[$companyName][$title][$amount]["invoiceId"] = $invoiceId;
			}else{
				$typesMap[$companyName][$title][$amount]["invoiceId"] = "0";
			}

			$typesMap[$companyName][$title][$amount]["amount"] = $amount;
			$typesMap[$companyName][$title][$amount]["adjustment"] = $adjustment;
			$typesMap[$companyName][$title][$amount]["minimum"] = $minimum;
		}
		
		$this->typesMap = $typesMap;

	}
    
	
	function getOrders($startDate, $endDate){
		
		return Order::where('created_at', ">=", $startDate)
					 ->where('created_at', "<=", $endDate)
					 ->with('checks')
					 ->orderBy('company_id', 'DESC')
					 ->get();
		
	}
	
	function getChecks($startDate, $endDate){
		
		return Check::where('completed_at', ">=", $startDate)
					 ->where('completed_at', "<=", $endDate)
					 ->with('order')
					 ->get();	
	}
	
	public function headings($reportType){
		
		$reportTypes = [
		   'full' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'custom' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'dailies' => "TIMESTAMP,CUSTOMER,TYPE,QTY,TOTALS\n",
		   'invoice_export' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'adminDashboard' => "CUSTOMER, TYPE, COUNT\n",
		   'limitedAdmin' => "",
		];
		
		return $reportTypes[$reportType];
    }
	
	public function addTransactionsToReport($reportType, $startDate, $endDate){
		
		Log::info("Library/Api/Admin/_ReportsLibrary::addTransactionsToReport ************************");
		
		$typesMap = $this->typesMap;
		$report = $this->headings($reportType);		
		$companyKeys = array_keys($typesMap);
		
		//sort the company keys?
		sort($companyKeys);

        //Log::info("Process company");
		//Log::info("# of companies = " . count($companyKeys));

		foreach($companyKeys as $companyName){

			$checkTypesData = $typesMap[$companyName];
			
			//Log::info("Process type data");
			foreach($checkTypesData as $typeData){
				
				
				
				foreach($typeData as $data){
							
					$company = json_decode($data["company"]);
					$typeId = $data["typeId"];
					$type = $data["description"];
					$quantity = $data["count"]; //total amount of checks
					$amount = $data["amount"];
					$total = $quantity * $amount;  //total dollar amount of checkType
					
					$invoiceId = $data['invoiceId'];
					
					//$invoiceId = 99999;

					$adjustment = $data["adjustment"];
					$minimum = $data["minimum"];
					
					$reconciled = false;
					
					$companyName = str_replace(",","", $company->company_name);
					
					if( $reportType == "invoice_export" || $reportType == 'custom'){
						$report .= $invoiceId . "," . $companyName . ",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," . $reconciled . "\n";
					}elseif($reportType == "adminDashboard"){
						$report .= "" . $companyName . "," . $type . "," . $quantity . "," . $company->company_id 
						        . "," . $startDate . "," . $endDate . "," . $typeId . "\n";
					}elseif($reportType == "dailies"){
						$report .= $startDate. "," . $companyName . "," . $type . "," . $quantity . "," . $total . "\n";
					}else{
						
						$report .= $invoiceId . "," . $companyName . ",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," . $reconciled . "\n";
					}
				}
			}
		}

        //$this->typesMap = [];
        
        Log::info("return from addTransactionsToReport");

	    return json_encode(['report'=>$report]);
		
	}

    public function limitedAdminCompanyReport(){}

    
}