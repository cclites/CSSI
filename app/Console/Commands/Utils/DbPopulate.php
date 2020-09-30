<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;

use App\Models\Check;

use Carbon\Carbon;
use DB;
use Log;

class DbPopulate extends Command
{
   protected $signature = 'populate_cssi_dev';
   protected $description = 'Load checks and appropriate records into the cssi_dev database.';
   
   //protected $companyId = null;

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
    	//sudo php artisan populate_cssi_dev zTcphM
    	
    	/*
        if( !is_null($this->argument('companyId')) ){
			echo "There is a companyId\n";
			//echo $this->argument('companyId') . "\n";
			$this->companyId = $this->argument('companyId');
		}else{
			echo "There is no companyId\n";
			//$this->$companyId = 0;
		}*/
		
		//If no company Id, get all checks for whatever time frame I want.
		
		//$startDate = Carbon::today()->startOfMonth()->startOfDay();
		//$endDate = Carbon::today()->subDay()->endOfDay();
		
		//Carbon::now('UTC');
		
		//$startDate = Carbon::now('UTC')->startOfMonth()->startOfDay();
		//$endDate = Carbon::now('UTC')->subDay()->endOfDay();
		
		$year = '2018';
		$month = '12';
		$day = '01';
		$tz = 'America/New_York';
		
		$startDate = Carbon::createFromDate($year, $month, $day, $tz)->startOfDay();
		$endDate = Carbon::today()->endOfDay();
		//echo "Start date: $startDate\n";
		//echo "End   date: $endDate\n";
		//$startDate = '2018-05-18 00:00:00';
		//$endDate = '2018-12-18 23:59:59';
		
		//return;
		
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		
		//if($this->companyId){
			
			//get checks for that user
			echo "Insert checks for company\n";
			
			/*
			DB::insert(
			 "Insert into cssi_dev.checks (select * from cssi.checks);"
			);
            */
			
			/*
			DB::insert(
				"Insert into cssi_dev.checks (select * from cssi.checks where company_id='". $this->companyId ."' and completed_at>='" . $startDate . "' and completed_at<='" . $endDate . "');"
			);
			 * 
			 */
			
			
			DB::insert(
				"Insert into cssi_dev.checks (select * from cssi.checks where completed_at>='" . $startDate . "' and completed_at<='" . $endDate . "');"
			);
			
			//******************
			
			echo "insert check_type for company\n";
			
			DB::insert(
			  "Insert into cssi_dev.check_type (select check_type.* from cssi.check_type, cssi_dev.checks where cssi_dev.checks.id = cssi.check_type.check_id);"
			);
			
			//******************

			echo "insert prices\n";
			DB::insert(
			  "Insert into cssi_dev.prices(select * from cssi.prices);"
			);
			
			//******************
			
			echo "insert other type records\n";
			
			DB::insert(
			  "Insert into cssi_dev.check_state(select check_state.* from cssi.check_state, cssi_dev.checks where cssi_dev.checks.id = cssi.check_state.check_id);"
			);
			
			DB::insert(
			  "Insert into cssi_dev.check_county(select check_county.* from cssi.check_county, cssi_dev.checks where cssi_dev.checks.id = cssi.check_county.check_id);"
			);
			
			DB::insert(
			  "Insert into cssi_dev.check_district(select check_district.* from cssi.check_district, cssi_dev.checks where cssi_dev.checks.id = cssi.check_district.check_id);"
			);
			
			DB::insert(
			  "Insert into cssi_dev.check_state_federal(select check_state_federal.* from cssi.check_state_federal, cssi_dev.checks where cssi_dev.checks.id = cssi.check_state_federal.check_id);"
			);
			
			DB::insert(
			  "Insert into cssi_dev.check_state_mvr(select check_state_mvr.* from cssi.check_state_mvr, cssi_dev.checks where cssi_dev.checks.id = cssi.check_state_mvr.check_id);"
			);
			
			//*****************
			
			//echo "insert 0-amount records\n";
			//DB::insert(
			  //"Insert into cssi_dev.transactions(select * from cssi.transactions where amount=0);"
			  
			  //"INSERT into cssi_dev.transactions (select transactions.* from cssi.transactions, cssi_dev.checks WHERE amount=0 and cssi_dev.checks.id=cssi.transactions.check_id);"
			  
			//);
			
			/*
			DB::insert(
			  "INSERT into cssi_dev.transactions (select transactions.* from cssi.transactions, cssi_dev.checks WHERE cssi_dev.checks.id=cssi.transactions.check_id);"
			);
			*/
			//would be nice to update the transaction_id
			
			//"INSERT into cssi_dev.transactions (select transactions.* from cssi.transactions, cssi_dev.checks WHERE cssi_dev.checks.id=cssi.transactions.check_id);"
						
		/*	
		}else{
			
			echo("Not ready to run all users.\n");
		}*/
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
		
    }
}
