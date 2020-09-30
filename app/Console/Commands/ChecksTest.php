<?php
	namespace App\Console\Commands;
	
	use Illuminate\Console\Command;
	
	// Models
	use App\Models\User;
	use App\Models\Check;
	use App\Models\Checktype;
	use App\Models\Type;
	use App\Models\Price;
	use App\Models\Report;
	use App\Models\Transaction;
	
	use Carbon\Carbon;
	use Exception;
	use Log;
	use Crypt;
	use DB;
	
	class ChecksTest extends Command{
	
		protected $signature = 'checks_test';
		protected $description = 'Get checks for the date range based on UTC time.';
		
		public function __construct()
	    {
	        parent::__construct();
		}
		
		public function handle(){
			
			$transactions = Transaction::whereBetween('created_at', [
				                \Carbon\Carbon::now()->subMonth()->startOfMonth()->subDay()->startOfDay(),
								\Carbon\Carbon::now()->subMonth()->endOfMonth()->endOfDay()
				            ])
							->get();
			
			echo $transactions->count() . "\n";
			
			
			$checks = Checktype::whereBetween('completed_at', [
							Carbon::today()->startOfMonth()->subMonth()->startOfDay(),
							Carbon::today()->subMonth()->endOfMonth()->endOfDay()
						])
						->get();
							
							
			echo $checks->count() . "\n";
	
			//echo json_encode($checks) . "\n";
			
			//$checks = Check::where('completed_at', '>' , '2018-07-31')
							  //->where('completed_at', '<', '2018-09-01')
							  //->first();
							  
            
			//echo $checks->count() . "\n";
			//echo json_encode($checks) . "\n";
		}
	}   