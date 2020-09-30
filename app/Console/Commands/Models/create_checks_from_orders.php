<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* _Models  */
use App\_Models\Check;
use App\_Models\Checktype;
use App\_Models\Company;
use App\_Models\Order;
use App\_Models\Price;


/* Facades */
use Carbon\Carbon;
use DB;
use Log;

class create_checks_from_orders extends Command
{
   	protected $signature = 'create_checks_from_orders';
	protected $description = 'convert checks from old format to new format';
	
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	//3568

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	
		echo "Creating checks from orders\n";
		
    	$start = Carbon::today()->startOfDay()->startOfYear();
		//$end = Carbon::now()->endOfDay();
		
		$startDate = $start->setTimezone('UTC');
		//$endDate = $end->setTimezone('UTC');
		
		$originalIds = [179744,179747,179750,179751,179752,179756,179758,179759,179773];
		
		$cnt = 0;
		
		$cOrders = DB::table("_orders")->whereIn('original_id', $originalIds)->get();
		
		echo "Order count is " . $cOrders->count() . "\n";
		//echo json_encode($cOrders) . "\n";
		
		//$cOrders = Order::where('created_at', ">", $startDate);
		//$cOrders = Order::all();
		
		//echo "ORder count is " . $cOrders->count() . "\n";
		
		
		foreach($cOrders as $cOrder){
			createChecksFromOrder($cOrder);
			$cnt += 1;
		}
		
		echo "Checks created: $cnt\n";
		//echo "Number missed = $missing\n";
    }


    public function createCheck(){
    	
    }

}
