<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;

use DB;
use Log;

class DbClear extends Command
{
   	protected $signature = 'clear_cssi_dev';
	protected $description = 'Empty the cssi_dev DB except for user records';
	
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
        echo "Empty the cssi_dev database\n";
		
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		
		
        DB::table('cssi_dev.check_county')->truncate();
		echo "Emptied cssi_dev.check_county\n";
		
		DB::table('cssi_dev.check_district')->truncate();
		echo "Emptied cssi_dev.check_district\n";
		
		
		DB::table('cssi_dev.check_state')->truncate();
		echo "Emptied cssi_dev.check_state\n";
		
		DB::table('cssi_dev.check_state_federal')->truncate();
		echo "Emptied cssi_dev.check_state_federal\n";
		
		DB::table('cssi_dev.check_state_mvr')->truncate();
		echo "Emptied cssi_dev.check_state_mvr\n";
		
		DB::table('cssi_dev.check_type')->truncate();
		echo "Emptied cssi_dev.check_type\n";
		
		DB::table('cssi_dev.transactions')->truncate();
		echo "Emptied cssi_dev.transactions\n";
		
		DB::table('cssi_dev.invoices')->truncate();
		echo "Emptied cssi_dev.invoices\n";
		
		DB::table('cssi_dev.checks')->truncate();
		echo "Emptied cssi_dev.checks\n";
		
		DB::table('cssi_dev.report')->truncate();
		echo "Emptied cssi_dev.report\n";
		
		DB::table('cssi_dev.profiles')->truncate();
		echo "Emptied cssi_dev.profiles\n";
		
		DB::table('cssi_dev.prices')->truncate();
		echo "Emptied cssi_dev.prices\n";
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
		
		echo "cssi_dev database has been cleared\n";
    }
}
