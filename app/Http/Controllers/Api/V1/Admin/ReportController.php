<?php


namespace App\Http\Controllers\Api\V1\Admin;

// Models
use App\Models\User;
use App\Models\Company;
use App\Models\Transaction;
use App\Models\Type;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\State;
use App\Models\County;
use App\Models\Invoice;

// Transformers
use \App\Transformers\Api\V1\TransactionTransformer;
use \App\Transformers\Api\V1\UserTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Mail;
use DB;
use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use Cache;

/*
 * This should actually be called DashboardController - not report controller.
 */

class ReportController extends Controller
{
	
	protected $cssiData = [11,12,13];
	
    public function checksForDay(Request $request){
    	
		Log::info("Api/V1/Admin/ReportController:checksForDay");
		
		$today = Carbon::today()->startOfDay();
		$today->setTimezone('UTC');
		
		$checks = \App\_Models\Check::where('created_at', ">=", $today)
		          ->where('sandbox', false)
		          ->whereNotIn('type', $this->cssiData)
		          ->get();
				  
		$completedChecksCount = \App\_Models\Check::where('completed_at', ">=", $today)
		                        ->where('sandbox', false)
		                        ->whereNotIn('type', $this->cssiData)
		                        ->count();

		$orderCount = \App\_Models\Order::where('created_at', ">=", $today)
		              ->where('sandbox', false)
		              ->count();
		
		$totalAmount = 0;
		
		foreach($checks as $check){
			$totalAmount += $check->amount;
		}
		
		$data = $this->calculateTotals($checks);
		$data["orderCount"] = $orderCount;
        $data["totalAmount"] = $totalAmount;
		$data["completedChecksCount"] = $completedChecksCount;
		
		return $data;
    }
	
	public function checksForMonth(Request $request){
		 
		Log::info("Api/V1/Admin/ReportController:checksForMonth");

		$start = Carbon::today()->startOfMonth()->startOfDay();
	    $start->setTimezone('UTC');
	    
	    $end = Carbon::today()->endOfDay();
	    $end->setTimezone('UTC');
		
		$checks = \App\_Models\Check::where('created_at', '>=', $start)
		          ->where('created_at', '<=', $end)
				  ->where('sandbox', false)
				  ->whereNotIn('type', $this->cssiData)
				  ->get();
				  
		$completedChecksCount = \App\_Models\Check::where('completed_at', '>=', $start)
						          ->where('completed_at', '<=', $end)
								  ->where('sandbox', false)
								  ->whereNotIn('type', $this->cssiData)
								  ->count();
		
		$orderCount = \App\_Models\Order::where('created_at', '>=', $start)
		              ->where('created_at', '<=', $end)
				      ->where('sandbox', false)
				      ->count();
				  
		$totalAmount = 0;
		
		foreach($checks as $check){
			$totalAmount += $check->amount;
		}

		$data = $this->calculateTotals($checks);
		$data["orderCount"] = $orderCount;
		$data["totalAmount"] = $totalAmount;
		$data["completedChecksCount"] = $completedChecksCount;

		return $data;		
	}
	
	public function checksForPriorMonth(Request $request){

		Log::info("Api/V1/Admin/ReportController:checksForPriorMonth");

		$start = Carbon::today()->startOfMonth()->subMonth()->startOfDay();
	    $start->setTimezone('UTC');
	    
	    $end = Carbon::today()->startOfMonth()->subMonth()->endOfMonth()->endOfDay();
	    $end->setTimezone('UTC');

		$checksForPriorMonth = DB::table('_checks')->where('created_at', '>=', $start)
								->where('created_at', '<=', $end)
								->where('sandbox', false)
								->whereNotIn('type', $this->cssiData)
								->get();
								
		$completedChecksCount = \App\_Models\Check::where('completed_at', '>=', $start)
						          ->where('completed_at', '<=', $end)
								  ->where('sandbox', false)
								  ->whereNotIn('type', $this->cssiData)
								  ->count();
			
		$orderCount = \App\_Models\Order::where('created_at', '>=', $start)
		          ->where('created_at', '<=', $end)
				  ->where('sandbox', false)
				  ->count();
				  
		$totalAmount = 0;
		
		foreach($checksForPriorMonth as $forPriorMonth){
			$totalAmount += $forPriorMonth->amount;
		}
		
		$data = $this->calculateTotals($checksForPriorMonth);
		$data["orderCount"] = $orderCount;
		$data["totalAmount"] = $totalAmount;
		$data["completedChecksCount"] = $completedChecksCount;

		return $data;
		
	}
	
	public function checksForYtd(Request $request){
		
		Log::info("Api/V1/Admin/ReportController:checksForYtd");
		
		$start = Carbon::today()->startOfYear()->startOfDay();
	    $start->setTimezone('UTC');
	    
	    $end = Carbon::today()->endOfDay();
	    $end->setTimezone('UTC');
		
		$checksForPriorYear = DB::table('_checks')->where('created_at', '>=', $start)
							  ->where('created_at', '<=', $end)
							  ->where('sandbox', false)
							  ->whereNotIn('type', $this->cssiData)
							  ->get();
							  
		$completedChecksCount = \App\_Models\Check::where('completed_at', '>=', $start)
						          ->where('completed_at', '<=', $end)
								  ->where('sandbox', false)
								  ->whereNotIn('type', $this->cssiData)
								  ->count();
		
		$orderCount = \App\_Models\Order::where('created_at', '>=', $start)
		          ->where('created_at', '<=', $end)
				  ->where('sandbox', false)
				  ->count();
				  
		$totalAmount = 0;
		
		foreach($checksForPriorYear as $check){
			$totalAmount += $check->amount;
		}
		
		$data = $this->calculateTotals($checksForPriorYear);
		$data["orderCount"] = $orderCount;
		$data["totalAmount"] = $totalAmount;
		$data["completedChecksCount"] = $completedChecksCount;
		
		return $data;
	}

    public function calculateTotals($cks){
    	
		$checkCounts = [];
		$typesCount = cache('types')->count();
		$types = cache('types');
		$times = [];

		$totalAverage = $cks->average('amount');
		
        //Initialize the array
		foreach($types as $type){
			
			if(!in_array($type->id, $this->cssiData)){
				$checkCounts[$type->id] = [ 'count'=>0, 'title'=>$type->title, 'amount'=>0, 'color' => $type->color];
			}
			                          
		}
		
		foreach($cks as $check){
			
			if(!in_array($check->type, $this->cssiData) && isset($checkCounts[$check->type]) ){
				$checkCounts[$check->type]["count"] += 1;
				$checkCounts[$check->type]["amount"] += $check->amount;
				//$times[] = $check->created_at;
			}
				
		}
		
		$data = [
		            'orderCount' => 0,
		            'totalAmount' => 0,
		        	'counts' => $checkCounts,
		        	'totals' => [
		        	    'count' => $cks->count(),
		        	    'average' => $totalAverage,
		        	    'amount' => 0
 		        	],
 		        	'times' => $times
				];
		
		return $data;
    }
	
	// This needs to go to
	// Note to self: Go away? Go where? Way to leave a good comment!!!
	
	public function createCustomTransactionsReport(Request $request){
		
		Log::info("Api/V1/Admin/ReportController::customCheck");
		
		$rl = new \App\Http\Controllers\Library\Api\Admin\_ReportsLibrary;
		return $rl->createTransactionsReport($request->all());
	
	}
	
	/*
	 * Raw totals for prior month
	 */
	public function rawtotals(Request $request){
		
		Log::info("Api/V1/Admin/ReportController::rawtotals");
		
		//$report = "";
		
		
		
		$types = cache('types');
		
		//Get all of the raw counts for the last month
		//$start = Carbon::today()->startOfMonth()->subMonth()->startOfDay();
		$start = Carbon::today()->startOfMonth()->startOfDay();
		
	    $start->setTimezone('UTC');
		$start = $start->format('Ymd');
		
		Log::info("Raw total start = $start");
	    
	    //$end = Carbon::today()->subMonth()->endOfMonth()->endOfDay();
	    
	    $end = Carbon::today()->endOfMonth()->endOfDay();
		
		
	    $end->setTimezone('UTC');
		$end = $end->format('Ymd');
		
		Log::info("Raw total end = $end");
		
		$report = "TYPE,COUNT\n";
				  
		foreach($types as $type){
			
			$records = DB::table('_dailies')
		          	 ->where('day', '>=', $start)
		             ->where('day', '<', $end)
					 ->where('type', $type->id)
					 ->get();
					 
		    $totalCount = 0;
			
			foreach($records as $record){
				
				$totalCount += $record->total;
				
			}

			$report .= $type->title . "," . $totalCount . "\n";
			
				
			     
		}

		return $report;
		
	}
	
}
