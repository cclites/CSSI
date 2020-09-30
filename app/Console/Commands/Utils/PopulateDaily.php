<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;

use Carbon\Carbon;
use DB;
use Log;

use App\Models\Transaction;
use App\Models\Checktype;
use App\Models\Type;
use App\Model\Check;

class PopulateDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populateDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer daily totals into dailies database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$startDate = '2019-01-09';
        $year = '2019';
		$month = '01';
		$day = '09';
		$tz = 'America/New_York';
		$cssiData = [11,12,13];
		
		$today = Carbon::today();
		$y = $today->format('Y');
		$m = $today->format('m');
		$d = $today->format('d');
		
		$totalAmount = 0;
		$count = 0;
		$averages = [];
		$info = [];
		
		$getNext = true;
		
		$start = Carbon::createFromDate($year, $month, $day, $tz);
		
		$baseStart = $start->format('Y-m-d');
		$baseEnd = Carbon::createFromDate($y, $m, $d, $tz)->format('Y-m-d');
		
		//$start = $baseStart;
		
		while($baseStart <= $baseEnd){
			
			$transactions = Transaction::where('date', $baseStart)->where('amount', ">", 0)->with('check')->get();
			
			echo "getting transactions\n";
			
			if(!$transactions->count() > 0){	
				echo "No transactions for $start\n";
				//die();
			}else{
				
				echo "Process Transactions\n";
				$totalAmount = $transactions->sum('amount');

				foreach($transactions as $transaction){
					
					$types = explode(",", $transaction->check_type);
			
					foreach($types as $type){
						
						if($type){
							$type = intval($type);
							
							if(!in_array($type, $cssiData)){
								$count += 1;
							}	
						}
						
					}
					
					$checktypes = $transaction->check->checktypes;
					$averages = $this->generateAverageAmountByType($checktypes, $averages);
					
				}
				
				$checks = $this->calculateTypeTotals($transactions);
				
				DB::table('dailies')->insert(['amount'=>$totalAmount, 'total'=>$count, 'averages'=>json_encode($averages), 'checks'=>json_encode($checks), 'added'=>Carbon::now()]);
				
				$totalAmount = 0;
				$checks = [];
				$averages = [];
				
				
				echo "Added Record\n";
				//echo json_encode($info) . "\n\n";
				sleep(1);
				
			}
			
			$count = 0;
			$info = [];
			$start = $start->addDay();
			$baseStart = $start->format('Y-m-d');
			
			//if($baseStart > $baseEnd){
				
			//}
		}
		
	
		echo "Done\n";
		
    }

	public function generateAverageAmountByType($checktypes, $averages){
		
		//$averagesByCheck = [];
		
		//Log::info("****** generateAverageAmountByType");
		//{"id":380,"check_id":129304,"type_id":10,"completed_at":"2018-12-18 15:00:12","created_at":"2018-04-23 12:02:21","enabled":1}
		
		
		foreach($checktypes as $checktype){
			
			//$check = Check::where('id', $checktype->id)
			
			//echo json_encode($checktype) . "\n";
			//Have to get the check to find the owner id to find the prices.
			
			if( isset($averages[$checktype->type_id]["count"]) ){
				
				//$averages[$checktype->type_id]["amount"] += $checktype->price();
				$averages[$checktype->type_id]["count"] += 1;
				$averages[$checktype->type_id]["timestamp"][] = $checktype->created_at;
				$averages[$checktype->type_id]["average"] = $averages[$checktype->type_id]["amount"]/$averages[$checktype->type_id]["count"];
				
			}else{

				//$averages[$checktype->type_id]["amount"] = $checktype->price();
				$averages[$checktype->type_id]["count"] = 1;
				$averages[$checktype->type_id]["timestamp"][] = $checktype->created_at;
			    $averages[$checktype->type_id]["average"] = $averages[$checktype->type_id]["amount"]/$averages[$checktype->type_id]["count"];
			}
			
		}
		
		//Log::info("***** AFTER GENERATING AVERAGES");
		
		return $averages;
		
	}
	
	public function calculateTypeTotals($transactions){
		
		$typeObjs = cache("types");

		$typeCount = Type::count();
		$instantiateArray = array_fill(1, $typeCount, 0);
		$totals = [];
		
		$totalsArray = [];
		
		foreach($transactions as $transaction){
			
			$types = explode(",", $transaction->check_type);

			foreach($types as $type){
				
				if($type){
					
					$type = intval($type);
					
					if( !in_array($type, [11,12,13]) ){
						
						/*
						if(isset($totalsArray[$type])){
							
							$totalsArray[$type]["count"] += 1;
							
						}else{
							
							$totalsArray[$type]["count"] = 1;
						}*/
						
						if(isset($totals[$type])){
							$totals[$type]['count'] += 1;
							//$totals[$type]['times'][] = $type->created_at;
						}else{
							$totals[$type]['count'] = 1;
							$totals[$type]['title'] = $typeObjs[$type-1]->title;
							$totals[$type]['type_id'] = $typeObjs[$type-1]->id;
							$totals[$type]['color'] = $typeObjs[$type-1]->color;
							//$totals[$type]['times'][] = $type->created_at;
						}	
					}	
				}
			}
		}
		
		//Log::info(json_encode($totals));
		
		return $totals;
		
	}
}
