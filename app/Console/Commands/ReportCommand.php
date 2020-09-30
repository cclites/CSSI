<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models


use Carbon\Carbon;
use Exception;
use Log;
use DB;
use Cache;

// Notifications




class ReportCommand extends Command{
	
	protected $signature = 'update {recordType}';
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		$recordType = $this->argument('recordType');
		$rc = new \App\Http\Controllers\Api\V1\Admin\ReportController();
		$request = new \Illuminate\Http\Request();
		
		if($recordType == 'ytd'){
			
			Cache::forget('ytd');
			$rc->checksForYtd($request);
			
		}elseif($recordType == 'cfm'){
	
			Cache::forget('cfm');
			
			$rc->checksForDay($request);
			$rc->checksForMonth($request);
			
		}elseif($recordType == 'cpm'){
			
			Cache::forget('cpm');
			$rc->checksForPriorMonth($request);
			
		}elseif($recordType == 'daily'){
			
			//Cache::forget('ytd');
			Cache::forget('cfm');
			Cache::forget('cfd');
			Cache::forget('cpm');
			
			$rc->checksForDay($request);
			$rc->checksForMonth($request);
			$rc->checksForPriorMonth($request);
			$rc->checksForYtd($request);
			
			echo "Completed all\n";
		}
	}
}