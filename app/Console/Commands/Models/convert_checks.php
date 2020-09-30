<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* Models  */
use App\_Models\Check;
use Carbon\Carbon;

use DB;
use Log;

class convert_checks extends Command
{
   	protected $signature = 'convert_checks';
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
    	
        $checks = \App\Models\Check::all()->where('created_at', ">=", '2019-01-01 00:00:00');
		
		
		foreach($checks as $check){
			
			
			$c = new \App\_Models\Check;
			
			$c->created_at = $c->updated_at = Carbon::now();
			$c->provider_reference_id = $check->provider_reference_id;
			$c->order_id = $check->id;
			$c->amount = 0;
			$c->save();
			
		}
		
    }

}
