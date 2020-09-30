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

class convert_checks_to_orders extends Command
{
   	protected $signature = 'convert_checks_to_orders';
	protected $description = 'convert checks to _orders';
	
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
    	
		$start = Carbon::today()->startOfDay()->startOfYear();
		$end = Carbon::now()->endOfDay();
		
		$startDate = $start->setTimezone('UTC');
		$endDate = $end->setTimezone('UTC');

		$checks = \App\Models\Check::where('created_at', ">=", $startDate)->get();

		foreach($checks as $check){
				
			$order = convertCheckToOrder($check);

		}
    }


    public function createCheck(){
    	
    }

}
