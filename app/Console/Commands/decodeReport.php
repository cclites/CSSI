<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\Type;
use App\Models\Report;
use App\Models\Price;
use App\Models\Profile;


use DB;
use Log;

use Carbon\Carbon;
use Exception;


/*
 * Script to update test server to add prices into the price
 * table for all users.
 * 
 * Is run manually.
 */
class decodeReport extends Command{
	
	protected $signature = 'decodeReport {reportId}';
	protected $description = "Script to decode reports";
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		$profile = Profile::where('check_id', 170200)->first();
		$dProfile = decrypt($profile->profile);
		$profile = json_decode($dProfile);
		echo "Data for Adkins\n" . $profile->ssn . "\n";
		
		$profile = Profile::where('check_id', 170035)->first();
		$dProfile = decrypt($profile->profile);
		$profile = json_decode($dProfile);
		echo "Data for Green\n" . $profile->ssn . "\n";
		
		$profile = Profile::where('check_id', 170202)->first();
		$dProfile = decrypt($profile->profile);
		$profile = json_decode($dProfile);
		echo "Data for Harper\n" . $profile->ssn . "\n";
				
	}
	

}