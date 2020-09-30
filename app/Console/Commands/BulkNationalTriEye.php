<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Type;
use App\Models\Price;
use App\Models\Report;
use App\Models\Transaction;

use App\Jobs\BulkNationalTriEyeCheck;

use Carbon\Carbon;
use Exception;
use Log;
use Crypt;
use DB;

class BulkNationalTriEye extends Command{
	
	
	protected $signature = 'BulkNationalTriEye';
    protected $description = 'Process Bulk National Tri-Eye.';
	
	public function __construct()
    {
        parent::__construct();
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function handle(){
		
		//get all of the saved uploaded files
		
		
		
		/*
		if(file_exists(storage_path("app/batch"))){
			return;
		}
		 * 
		 */
		
		$directory = storage_path("app/batch");
        $files = array_diff(scandir($directory), array('..', '.'));
		
		/*
		if(!$files){
			Log::info("No files to upload")
			return;
		}
		 * 
		 */
		
		//Log::info("Directory is $directory");
		
		//Log::info(json_encode($files));

		
        foreach($files as $fileName){
        	
			Log::info("Running BulkNationalTri-Eye");
        	
			$tuples = explode(".", $fileName);
			$companyId= $tuples[0];
			
			$user = User::where("company_id", $companyId)->where('company_rep', true)->first();
			Log::info(json_encode($user));

			$path = storage_path("app/batch/" . $fileName);
			$file = stripcslashes( file_get_contents($path) );
			
			dispatch(new BulkNationalTriEyeCheck($file, $user, 1, $fileName));
        	
        }
		
		
		
	}
}