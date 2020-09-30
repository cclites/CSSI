<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Type;
use App\Models\Price;
use App\Models\Transaction;

use Carbon\Carbon;
use Exception;
use Log;
use DB;

class RemoveChecks extends Command{

	protected $signature = 'removeChecks {--now} {--cssi_data}';
	protected $description = 'Remove test checks older than 7 days, and checks older than 2 years.';
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		/*
		$checks = Check::whereDate('created_at', "<=", Carbon::now()->subDays())
		          ->where('sandbox', true)
				  ->get();
		 * 
		 */
				  
		//$this->remove($checks);

	    if($this->option('now')){
	    	
			Log::info("Remove Checks Now!");
	 	
		    /*
			$checks = Check::whereDate('created_at', ">=", Carbon::now()->subDay())
	          	->where('sandbox', true)
				->where('user_id', 3159)
			  	->get();
			 * 
			 */
			 
			 
			 $checks = Check::where('sandbox', true)
			           ->where('created_at', ">=", Carbon::today()->startOfYear()->startOfDay())
				       ->get();
					   
		     echo $checks->count() . "\n";
			/* 
			$checks = Check::where('sandbox', true)
				->where('user_id', 3159)
			  	->get();
		    */
		    
			$this->remove($checks);
	 	}
		
		if($this->option('cssi_data')){
			
			Log::info("Remove CSSI Checks");
			
			$checks = Check::hasCssiData()
	          	->where('sandbox', true)
				->where('user_id', 3159)
			  	->get();

			//print_r($checks);
			echo($checks->count() . "\n");
			
			$this->remove($checks);
			
		}

		$checks = Check::whereDate('created_at', "<=", Carbon::now()->subYears(2))
				  ->get();

        //$this->remove($checks);
		
	}

	

	public function remove($checks){
		
		foreach($checks as $check){
			
			echo "Removing check\n";
			echo json_encode($check) . "\n";
			sleep(1);
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0');
			
	        DB::table('check_county')->where('check_id', $check->id)->delete();
	        DB::table('check_district')->where('check_id', $check->id)->delete();
	        DB::table('check_state')->where('check_id', $check->id)->delete();
	        DB::table('check_state_federal')->where('check_id', $check->id)->delete();
	        DB::table('check_type')->where('check_id', $check->id)->delete();
	        DB::table('educations')->where('check_id', $check->id)->delete();
	        DB::table('employments')->where('check_id', $check->id)->delete();
	        DB::table('mvrs')->where('check_id', $check->id)->delete();
	        DB::table('transactions')->where('check_id', $check->id)->delete();
			
			DB::table('report')->where('check_id', $check->id)->delete();
			
			$check->delete();
			
			DB::table('_orders')->where('original_id', $check->id)->delete();
			DB::table('_checks')->where('original_id', $check->id)->delete();
			
			DB::statement('SET FOREIGN_KEY_CHECKS=1');
		}
		
	}
	
}
