<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* _Models  */
use App\_Models\Check;
use App\_Models\Order;

/* Models */
use App\Models\Type;


use Carbon\Carbon;

use DB;
use Log;

class model_queries extends Command
{
   	protected $signature = 'model_queries {mode?}';
	protected $description = 'misc query models';
	
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
    	
		//get counts by checktype
		$checks = Check::all();
		$typesCount = cache('types')->count();
		$types = cache('types');
		
		//$checkCounts = array_fill(0, $typesCount, 0);
		
		$checkCounts = [];
		
		foreach($types as $type){
			$checkCounts[$type->id] = [ 'count'=>0, 'title'=>$type->title, 'amount'=>0];                          
		}
		
		foreach($checks as $check){
			$checkCounts[$check->type]['count'] += 1;
			$checkCounts[$check->type]['amount'] += $check->amount;
		}
		
		echo json_encode($checkCounts) . "\n";
		
		/*
        $orders = Order::where('company_id', 'aaaaaa')->get();
		
		$totalAmount = 0;
		
		foreach($orders as $order){
			
			$checks = $order->checks;
			
			foreach($checks as $check){
				
				echo "Check amount is " . $check->amount . "\n";
				$totalAmount += $check->amount;
				
			}
			
		}
		
		echo "Total amount of checks is: $totalAmount\n"; 
		*/
    }

}
