<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Company;
use App\Models\Price;
use App\Models\State;
use App\Models\County;
use App\Models\Transaction;
use App\Models\Adjustment;
use App\Models\Minimum;
use App\Models\Type;

use Carbon\Carbon;

use Exception;
use Log;
use App;
use View;
use DB;

use Knp\Snappy\Pdf;

class CustomReport extends Command{
	
	protected $signature = 'custom_report';
    protected $description = 'Generate a custom report.';
	
	public function __construct()
    {
        parent::__construct();
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function handle($request){
	//public function handle(){
		
		$companies = Company::companies();
		$typeObjs = Type::all();
		$states = State::all();
		$counties = County::all();
		
		$startDate = isset($request->start) ? $request->start : null;
		$endDate = isset($request->end) ? $request->end : null;
		
		$start = null;
		$end = null;
		
		if( isset($startDate) ){
			$start = explode("-", $startDate);
		    $start = Carbon::create($start[0], $start[1], $start[2], 0, 0, 0);
		}
		
		if( isset($endDate) ){
			$end = explode("-", $endDate);
		    $end = Carbon::create($end[0], $end[1], $end[2], 0, 0, 0);
		}
		
		if(!isset($startDate) && !isset($endDate)){
			$start = Carbon::now()->startOfMonth()->startOfDay();
			$end = Carbon::now()->endOfMonth()->endOfDay();
		}
		
		$report = "Start:, $start\n";
		$report .= "End:, $end\n";
		
		$report .= "CUSTOMER,ADDRESS,CITY,STATE,ZIP,QTY,ITEM,PRICE,EXTENSION,EMAIL,ADJUSTMENT,MINIMUM\n";
		
		foreach($companies as $company){

			$prices = $company->owner()->priceArray();
			$transactions = Transaction::query();
			
			if( isset($start) && !isset($end) ){
				
				$transactions->where("created_at", ">=", $start);
				
			}elseif(isset($start) && isset($end)){
				
				$transactions->where("created_at", ">=", $start)
				   			 ->where("created_at", "<", $end);
							 
			}else{
				
				$transactions->whereBetween('created_at', [
				                  Carbon::now()->startOfMonth()->startOfDay(),
								  Carbon::now()->endOfMonth()->endOfDay()
				              ]);

			}
			
			//echo "After checking dates " . $transactions->count() . "\n";
			
			$transactions = $transactions->where('parent_id', $company->company_id)->orderBy('id', 'desc')->get();
			
			//print_r($transactions);
			//echo "\n";
			
			//echo "After getting by parent id " . $transactions->count() . "\n";
			
				

            /*
			$transactions = Transaction::where('parent_id', $company->company_id)
							->whereBetween('created_at', [
				                Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
								Carbon::now()->subMonth()->endOfMonth()->endOfDay()
				            ])
				            //->orderBy('id', 'desc')
							->get();
			 */
			 
			 /*
		    $transactions = Transaction::where('parent_id', $company->company_id)
							->whereBetween('created_at', [
				                Carbon::now()->startOfMonth()->startOfDay(),
								Carbon::now()->endOfMonth()->endOfDay()
				            ])
				            //->orderBy('id', 'desc')
							->get();
			  * 
			  */
			

			$typesMap = [];
			$adjustment = 0;
			$minimum = 0;

			$adj = \App\Models\Adjustment::where("company_id", $company->company_id)->first();
			$minim = \App\Models\Minimum::where("company_id", $company->company_id)->first();
			
			if($adj){
				$adjustment = $adj->amount;
			}
			
			if($minim){
				$minimum = $minim->amount;
			}
			
			foreach($transactions as $trans){

				$checkTypes = $trans->check->checktypes;

				foreach($checkTypes as $type){

					$typeId = $type->type_id;
					$title = $typeObjs[$type->type_id - 1]->title;
					$baseAmount = $prices[$typeId];
					$amount = $baseAmount;
					
					if($typeId == 3){
						$profile = json_decode(decrypt($trans->check->profile->profile));
						$stateId = $profile->state_tri_eye_state_ids[0];
						$stateCode = $states[$stateId-1]['code'];	
						$title .= " (" . $stateCode . ")";
						$amount = $baseAmount + $states[$stateId-1]["extra_cost"];
					    
					}elseif($typeId == 4){
						$profile = json_decode(decrypt($trans->check->profile->profile));
						$countyId = $profile->county_tri_eye_county_ids[0];
						$county = $counties->find($countyId - 1);
						$countyTitle = $county->title;
						$countyState = $county->state_code;
						$title .= " (" . $countyTitle . ":" . $countyState . ")";
					    $amount =  $baseAmount + $county->extra_cost;
					 
					}elseif($typeId == 10){
						$title .= " (" . $trans->check->states[0]->code . ")";
						$amount =  $baseAmount + $trans->check->states[0]->mvr_cost;
					}
                    
					if(isset($typesMap[$typeId][$title][$amount]["count"])){
						$typesMap[$typeId][$title][$amount]["count"] += 1;
					}else{
						$typesMap[$typeId][$title][$amount]["count"] = 1;
						$typesMap[$typeId][$title][$amount]["description"] = $title;
						$typesMap[$typeId][$title][$amount]["typeId"] = $typeId;
						$typesMap[$typeId][$title][$amount]["transactionId"] = $trans->id;

						$typesMap[$typeId][$title][$amount]["amount"] = $amount;
					}
					
	
				}//end foreach type
				
			}//end foreach transactions
			
            
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
						$transactionId = $d["transactionId"];
						
						$report .= "\"" . $company->data->company_name . "\",\"" . $company->data->address . "\","
					        	. $company->data->city . "," . $company->data->state . "," . $company->data->zip . ","
					        	. $quantity . "," . $type . "," . $amount . "," . $total . "," . $company->data->email . "," 
					        	. $adjustment . "," . $transactionId ."\n";
					}
				}
				
				
				$quantity = 0;
				$amount = 0;
			}

	
		}//end foreach company
		
        return $report;
		//print_r($report);

	}
	
}