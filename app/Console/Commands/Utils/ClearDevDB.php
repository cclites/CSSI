<?php
namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;

use Log;
use DB;



class ClearDevDB extends Command{
	
	protected $signature = 'clear_cssi_dev';
	protected $description = 'Empty the cssi_dev DB except for user records';
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		
		
        DB::table('cssi_dev.check_county')->truncate();
		
		/*
		DB::table('cssi_dev.check_state')->truncate();
		DB::table('cssi_dev.check_state_federal')->truncate();
		DB::table('cssi_dev.check_state_mvr')->truncate();
		DB::table('cssi_dev.check_type')->truncate();
		DB::table('cssi_dev.transactions')->truncate();
		DB::table('cssi_dev.invoices')->truncate();
		DB::table('cssi_dev.checks')->truncate();
		DB::table('cssi_dev.report')->truncate();
		DB::table('cssi_dev.profiles')->truncate();
		DB::table('cssi_dev.prices')->truncate();
		 */
		
		/*
		DB::table('cssi_dev.table_name')->truncate();
		DB::table('cssi_dev.table_name')->truncate();
		DB::table('cssi_dev.table_name')->truncate();
		DB::table('cssi_dev.table_name')->truncate();
		DB::table('cssi_dev.table_name')->truncate();
		*/
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
	
	}
}

