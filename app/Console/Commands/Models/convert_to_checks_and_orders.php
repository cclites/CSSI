<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* Models  */
use App\_Models\Check;
use Carbon\Carbon;

use DB;
use Log;

class convert_to_checks_and_orders extends Command
{
   	protected $signature = 'convert_to_checks_and_orders';
	protected $description = 'create orders from checks';
	
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
    	
		$start = Carbon::today()->startOfDay()->startOfYear();
		$end = Carbon::now()->endOfDay();
		
		$startDate = $start->setTimezone('UTC');
		$endDate = $end->setTimezone('UTC');
		
		//$checks = \App\Models\Check::where('created_at', ">=", $startDate)->where('created_at', "<=", $endDate)->get();
		
		$checks = \App\Models\Check::where('created_at', ">=", $startDate)->get();

		foreach($checks as $check){
				
			$order = convertCheckToOrder($check);

		}
    	
    }

}


