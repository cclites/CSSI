<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* Models  */
use App\Models\Check;
use Carbon\Carbon;

/* _Models */
use App\_Models\Order;

use DB;
use Log;

class create_orders_from_checks extends Command
{
   	protected $signature = 'create_orders_from_checks';
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	
        $checks = Check::all()->where('created_at', ">=", '2019-01-01 00:00:00');
		echo $checks->count() . "\n";
		
		
		foreach($checks as $check){
			
           
			if(!$check->transaction_id && $check->created_at<'2019-03-21 12:00:00'){
				echo "improper transaction id";
				Log::info(json_encode($check) . "\n");
				continue;
			}
			
			/*
			$c = new \App\_Models\Check;
			
			$c->created_at = $c->updated_at = Carbon::now();
			$c->provider_reference_id = $check->provider_reference_id;
			$c->order_id = $check->id;
			$c->amount = 0;
			$c->save();
			*/
		}
		
    }

}
