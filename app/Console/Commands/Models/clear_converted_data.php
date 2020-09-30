<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* _Models  */
use App\_Models\Check;
use App\_Models\Company;
use App\_Models\Order;
use App\_Models\Price;
use App\_Models\Invoice;


/* Facades */
use Carbon\Carbon;
use DB;
use Log;

class clear_converted_data extends Command
{
   	protected $signature = 'clear_converted_data';
	
	protected $description = 'Clear converted data from the _tables';
	
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
    public function handle(){
    	
		//Check::query()->delete();
		//Company::query()->delete();
		
		//Price::query()->delete();
		Invoice::query()->delete();
		
		
		//Order::query()->delete();
		
    }
	 
}

