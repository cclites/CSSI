<?php

namespace App\Http\Controllers\Library\Api\Admin;

//system
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

//models
use App\Models\Adjustment;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\District;
use App\Models\Invoice;
use App\Models\Minimum;
use App\Models\Transaction;
use App\Models\Company;
use App\Models\State;
use App\Models\County;
use App\Models\Type;
use App\Models\Profile;
use App\Models\Price;
use App\Models\User;

//facades
use Log;
use Auth;
use DB;
use Carbon\Carbon;


class ReportsLibrary{
	
	protected $stateObjs;
	protected $typeObjs;
	protected $countyObjs;
	protected $districtObjs;
	protected $typesMap;
	protected $userPrices;
	protected $reportType;

	
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
		
        Log::info("Library/Api/Admin/ReportsController::createTransactionReport");
		
		/** FIELDS **/
		$companyIds = isset($request["company"]) ? $request["company"] : null;
		$reportType = isset($request["report_type"]) ? $request["report_type"] : null;
		$startDate = isset($request["start"]) ? $request["start"] : null;
		$endDate = isset($request["end"]) ? $request["end"] : null;
		$report = "";
		
		
		$this->reportType = $reportType;
		
		if($reportType){
		    $report .= $this->headings($reportType);	
		}else{
		    $report .= $this->headings();	
		}
		

		if($reportType == 'invoice_export'){
			
			$invoiceRunDate = date('Y-m-') . "01";
			$invoices = Invoice::where('date', '>=', $invoiceRunDate)->get();
			
			//Log::info("Invoice count is " . $invoices->count());
			
			//add each check type to the typesMap
			foreach($invoices as $invoice){

				$transactions = Transaction::where('invoice_id', $invoice->id)->where("amount", ">", 0)->get();
				$this->userPrices = Price::where('user_id', $invoice->user_id)->get();
				$company = User::where('id', $invoice->user_id)->first();
				
				foreach($transactions as $transaction){
							
					/*
					   [2019-01-02 16:43:54] local.INFO: {"id":87086,"parent_id":"zTcphM","user_id":3162,"check_id":157636,"invoice_id":47403,"date":"2018-12-01","amount":"17.00","description":"Check (ID: 157636) for Goran  Nikolovski
$17 Motor Vehicle Report  (IL)","stripe_charge":null,"created_at":"2018-12-01 01:00:03","updated_at":"2019-01-01 11:03:46","check_type":"10,","notes":"201901","deleted":0,"testing":0}
					 */	

				    $checkId = $transaction->check_id;
					$checktypes = Checktype::where("check_id", $checkId)->get();
					
					$this->addChecktypesToMap($checktypes, $transaction, $invoice, $company);
					
				}
				
				
			}
			
			$report .= $this->addTransactionsToReport($reportType);
			
			
		}elseif($reportType == 'adminDashboard'){
				
			if($companyIds){
				$companies = Company::companies($companyIds);
			}else{
				$companies = Company::companies();
			}
			
			
			
			//$today = $request["start"];
			$today = Carbon::today()->format('Y-m-d');
			
			//Log::info($today);
			
			foreach($companies as $company){
				
				$transactions = Transaction::where('created_at', '>=', $today . " 00:00:00")->where('parent_id', $company->company_id)->get();
				
				//Log::info(json_encode($transactions));
				
				if($transactions->count() == 0){
					continue;
				}
				
				$this->userPrices = Price::where('user_id', $company->data->id)->get();
				
				//Log::info("Transactions Count " . $transactions->count());
				
				foreach($transactions as $transaction){
					
					$checkId = $transaction->check_id;
					$checktypes = Checktype::where("check_id", $checkId)->get();

					foreach($checktypes as $checkType){
					  $this->addChecktypesToMap($checktypes, $transaction, null, $company);
					}
					
					
				}
				
				
				
				$report .= $this->adminDashboardReport($company);
				$this->typesMap = [];
				
			}
			
			
			
		}/*elseif($reportType == 'custom'){
			
		}*/
				
		return json_encode(['report'=>$report]);
	}
	
	public function addChecktypesToMap($checktypes, $transaction, $invoice, $company){
			
		$reportType = $this->reportType;
		
		foreach($checktypes as $checktype){
			
			//Log::info(json_encode($checktype));
			
			$typeId = $checktype->type_id;
			$typeObj = $this->typeObjs->where('id', $typeId)->first();
			$title = $typeObj->title;
			
			$baseAmount = $this->userPrices->where('type_id', $typeId)->pluck('amount');
			$amount = $baseAmount[0];
		
	        if($typeId == 3){
						
				$checkState = DB::table('check_state')->where('check_id', $transaction->check_id)->get();
				
				foreach($checkState as $state){
					
					$stateId = $state->state_id;
					$stateObj = $this->stateObjs->where("id", $stateId)->first();
					$passthrough = $stateObj->extra_cost;
					$baseTitle = $title;
					
					if($reportType == 'full' || $reportType == 'custom' || $reportType == 'invoice_export'){
						$baseTitle .= " (" . $stateObj->code . ")";
					}
					
					$amount += $passthrough;
					$this->addToTypesMap($checktype, $baseTitle, $invoice, $amount, $company);
							
				}
			}elseif($typeId == 4){
				
				$checkCounty = DB::table('check_county')->where('check_id', $transaction->check_id)->get();
						
				foreach ($checkCounty as $county){
					
					$countyId = $county->county_id;
					
					$countyObj = $this->countyObjs->where('id', $countyId)->first();
					$passthrough = $countyObj->extra_cost;
					$baseTitle = $title;

					if($reportType == 'full' || $reportType == 'custom' || $reportType == 'invoice_export'){
						$baseTitle .= " (" . $countyObj->title . ":" . $countyObj->state_code . ")";
					}
					
					$amount += $passthrough;

					$this->addToTypesMap($checktype, $baseTitle, $invoice, $amount, $company);
				}
				
			}elseif($typeId == 6){
				$checkState = DB::table('check_state_federal')->where('check_id', $transaction->check_id)->get();
				
				foreach($checkState as $state){
					
					$stateId = $state->state_id;
					
					$stateObj = $this->stateObjs->where("id", $stateId)->first();
					$passthrough = $stateObj->extra_cost;
					
					$baseTitle = $title;
					
					if($reportType == 'full' || $reportType == 'custom' || $reportType == 'invoice_export'){
						$baseTitle .= " (" . $stateObj->code . ")";
					}
					
					$amount += $passthrough;
					
					$this->addToTypesMap($checktype, $baseTitle, $invoice, $amount, $company);
						
				}
				
			}elseif($typeId == 7){
						
				$checkDistrict = DB::table('check_district')->where('check_id', $transaction->check_id)->get();
				
				foreach($checkDistrict as $district){
					
					$districtId = $district->id;
					$districtObj = $this->districtObjs->where('id', $district->id)->first();
					
					
					$baseTitle = $title;
					
					if($reportType == 'full' || $reportType == 'custom' || $reportType == 'invoice_export'){
						$baseTitle .= " (" . $districtObj->state_code . " ," . $districtObj->title  . ")";
					}
					
					$this->addToTypesMap($checktype, $baseTitle, $invoice, $amount, $company);
				}
				
				//continue;
				
			}elseif($typeId == 10){
						
						
				$checkState = DB::table('check_state')->where('check_id', $transaction->check_id)->first();
				$stateId = $checkState->state_id;
				
				$state = $this->stateObjs->where("id", $stateId)->first();
				$passthrough = $state->mvr_cost;
				
				$baseTitle = $title;
				
				if($reportType == 'full' || $reportType == 'custom' || $reportType == 'invoice_export'){
					$baseTitle .= " (" . $state->code . ")";
				}
				
                $amount += $passthrough;
				$this->addToTypesMap($checktype, $baseTitle, $invoice, $amount, $company);

			}else{
				$this->addToTypesMap($checktype, $title, $invoice, $amount, $company);
			}
			
		}

	}

    public function addToTypesMap($checktype, $title, $invoice, $amount, $company){
	//public function buildTypeMap(&$typesMap, $typeId, $title, $amount, $transactionId, $invoiceId = null){
				
		Log::info("Add types to map");	
		
		$typesMap = $this->typesMap;
		$typeId = $checktype->type_id;
		$companyName = $company->company_name;
		
				
		if(isset($typesMap[$companyName][$title][$amount]["count"])){
			$typesMap[$companyName][$title][$amount]["count"] += 1;
		}else{
			$typesMap[$companyName][$title][$amount]["count"] = 1;
			$typesMap[$companyName][$title][$amount]["description"] = $title;
			$typesMap[$companyName][$title][$amount]["typeId"] = $typeId;
			$typesMap[$companyName][$title][$amount]["transactionId"] = 0;
			
			if( $invoice ){
				$typesMap[$companyName][$title][$amount]["invoiceId"] = $invoice->id;
			}else{
				$typesMap[$companyName][$title][$amount]["invoiceId"] = "0";
			}
            
			$typesMap[$companyName][$title][$amount]["amount"] = $amount;
		}
		
		$this->typesMap = $typesMap; 
		
		//Log::info(json_encode($typesMap[$typeId][$title]));
	}


	//public function process check_

	public function addTransactionsToReport($reportType){
		
		
		$typesMap = $this->typesMap;
		$report = "";
		
		
		$companyKeys = array_keys($typesMap);
		
		foreach($companyKeys as $companyName){
			
			//Log::info(json_encode($checkData) . "\n");
			//continue;
				
			$checkTypesData = $typesMap[$companyName];
			//"Motor Vehicle Report (GA)":{"11":{"count":66,"description":"Motor Vehicle Report (GA)","typeId":10,"transactionId":0,"invoiceId":47403,"amount":11
			
			foreach($checkTypesData as $typeData){
				
				
				foreach($typeData as $data){
					
					//[2019-01-02 18:10:21] local.INFO: {"count":165,"description":"National Tri-Eye Check","typeId":1,"transactionId":0,"invoiceId":47491,"amount":"7.00"}
  					//[2019-01-02 18:10:21] local.INFO: {"count":1,"description":"National Tri-Eye Check","typeId":1,"transactionId":0,"invoiceId":47492,"amount":"10.00"}
					
					$typeId = $data["typeId"];
					$type = $data["description"];
					$quantity = $data["count"];
					$amount = $data["amount"];
					$total = $quantity * $amount;
					$invoiceId = $data["invoiceId"];
					$adjustment = 0;
					$minimum = 0;
					$reconciled = false;
					
					$company = User::where("company_name", $companyName)->where("company_rep", true)->first();
					
					if($reportType == "custom"){
						$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," /*. $transactionId . ","*/ . $reconciled . "\n";
					}elseif($reportType == "full" || $reportType == "invoice_export"){
						$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," /*. $transactionId . ","*/ . $reconciled . "\n";
					}elseif($reportType == 'adminDashboard'){
						
					}
					
					
				}
				//Log::info(json_encode($d) . "\n");
				
				//{"23":{"count":1,"description":"Motor Vehicle Report (CT)","typeId":10,"transactionId":0,"invoiceId":47489,"amount":23}}
				
				/*
					$typeId = $data["typeId"];
					$type = $data["description"];
					$quantity = $data["count"];
					$amount = $data["amount"];
					$total = $quantity * $amount;
					$invoiceId = $data["invoiceId"];
					$adjustment = 0;
					$minimum = 0;
					$reconciled = false;
				
				//will need to look up company
				$company = User::where("company_name", $companyName)->where("company_rep", true)->first();
				
				if($reportType == "custom"){
					$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
				        	. $company->city . "," . $company->state . "," . $company->zip . ","
				        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
			        		. $adjustment . "," . $minimum . "," . $transactionId . "," . $reconciled . "\n";
				}elseif($reportType == "full" || $reportType == "invoice_export"){
					$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
				        	. $company->city . "," . $company->state . "," . $company->zip . ","
				        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
			        		. $adjustment . "," . $minimum . "," . $transactionId . "," . $reconciled . "\n";
				}
				*/
				
				
			}
			
		}
		
		Log::info("Done");
		
	    return $report;
		
		
		
		//$company->data = $company;
		
		foreach($typesMap as $map=>$data){

			$keys = array_keys($data);
			
			foreach($keys as $k){
				
				$dataKeys = array_keys($data[$k]);
				
				
				foreach($dataKeys as $dk){

					$d = $data[$k][$dk];
					
					$typeId = $d["typeId"];
					$type = $d["description"];
					$quantity = $d["count"];
					$amount = $d["amount"];
					$total = $quantity * $amount;

					$invoiceId = $d["invoiceId"];
					$adjustment = 0;
					$minimum = 0;
					$reconciled = false;
					
					if($reportType == "custom"){
						$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," /*. $transactionId . ","*/ . $reconciled . "\n";
					}elseif($reportType == "full" || $reportType == "invoice_export"){
						$report .= $invoiceId . ",\"" . $company->company_name . "\",\"" . $company->address . "\","
					        	. $company->city . "," . $company->state . "," . $company->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->email . "," 
				        		. $adjustment . "," . $minimum . "," /*. $transactionId . ","*/ . $reconciled . "\n";
					}
						
				}
				
				$adjustment = "0";
				$minimum = "0";
			}
		}

		return $report; 
		
	}
	

	public function adminDashboardReport($company){

		$typesMap = $this->typesMap;
				
		$start = Carbon::today()->format('Y-m-d');
		$end = Carbon::today()->addDay()->format('Y-m-d');
		//$end = Carbon::now();
		
		$report = "";
		
		foreach($typesMap as $map=>$data){
			
			foreach($data as $dat){
			
			    foreach($dat as $d){
			    	
					//Log::info(json_encode($d));
					$report .= str_replace(",", "", $company->data->company_name) . "," . $d["description"] . ',' . $d["count"] . ',' .  $d["typeId"] . "," .  $start . "," . $end . "," . $company->data->company_id . "\n";
				}	
				
			}
				
				/*
				Log::info(json_encode($dat));
				
				//$report .= '"' . $company->data->company_name . '",' . $d["description"] . ',' . $d["count"] . ',' .  $d["typeId"] . "," .  $d["start"] . "," . $d["end"] . "," . $company->data->company_id . "\n";
				//$day = $d["start"]->subDay();
				
				$report .= str_replace(",", "", $company->data->company_name) . "," . $d["description"] . ',' . $d["count"] . ',' .  $d["typeId"] . "," .  $d["start"] . "," . $d["end"] . "," . $company->data->company_id . "\n";
				*/
			//}
			
		}
		
		return $report;
		
	}

	

	public function headings($reportType = "custom"){
    	
		$reportTypes = [
		   'full' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'custom' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'invoice_export' => "INVOICE,CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM,RECONCILED\n",
		   'adminDashboard' => "CUSTOMER, TYPE, COUNT\n",
		   'limitedAdmin' => "",
		];
		
		return $reportTypes[$reportType];
    }
	
}