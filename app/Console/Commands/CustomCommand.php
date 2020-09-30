<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

//use App\Http\Controllers\Api\V1\CheckController;

// Models
/*
use App\Models\User;
use App\Models\Customer;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\Type;
use App\Models\Price;
use App\Models\Transaction;
use App\Models\Report;
use App\Models\Stat;
use App\Models\State;
use App\Models\County;
*/

use App\_Models\Company;
//use App\_Models\Check;
use App\_Models\Invoice;
//use App\_Models\Price;
//use App\_Models\Order;

//use App\Models\Check;
use App\Models\Type;
use App\Models\Checktype;

use Carbon\Carbon;
use Exception;
use Log;
use DB;
use Crypt;
use Hash;
use View;

use App;
use Knp\Snappy\Pdf;

// Notifications


class CustomCommand extends Command{
	
	protected $signature = 'custom';
	
	protected $month = 'April';
	protected $testing = false;

	public function __construct(){
        parent::__construct();
    }
	
	
	public function handle(){
		
		$invoice = Invoice::find(8170);
		$path = $this->renderInvoice($invoice);
		
			
		//$start = Carbon::today()->startOfDay()->startOfMonth()->subMonth();
		//$end = Carbon::today()->endOfDay()->endOfMonth()->subMonth();
		//$companies = DB::table("_companies")->get();
		
		/*
		foreach ($companies as $company) {
			
			echo "******" . $company->company_name . "\n";
			$recipients = [];
		
			if($company->invoice_recipients){
				$recipients = explode(",", $company->invoice_recipients);
			}else{
				$recipients[] = $company->email;
			}
			
			foreach($recipients as $r){
				
				$r = trim($r);
				
				echo "-----    $r\n";
				
			}
			
		}*/
		
					
	}
	
	public function renderInvoice($invoice){
		
		//echo json_encode($invoice) . "\n";
		
		$companyId = $invoice->company_id;
		
		$nonce = createSeed(4);
		$genDate = \Carbon\Carbon::today()->format("Y_m");
		$path = public_path("pdf/" . $this->month ."_2019/" . $genDate . "_" . $companyId . "_" . $nonce . ".pdf");
		
		$pdf = App::make('snappy.pdf.wrapper');
		$view = View::make('invoices/_single', ['invoice'=>$invoice])->render();
		$pdf->generateFromHtml($view, $path);
		
		/*
		echo "Rendering Invoice # " . $invoice->id . "\n";
		echo "$companyId\n";
		
		$company = Company::where('id', $companyId)->first();
		echo json_encode($company) . "\n";
		
		
		
		
		
		$genDate = \Carbon\Carbon::today()->format("Y_m");
		$path = public_path("pdf/" . $this->month ."_2019/" . $genDate . "_" . $company->company_id . "_" . $nonce . ".pdf");
		
		
		//echo "$path\n";
		/*
		

		$pdf = App::make('snappy.pdf.wrapper');
		$view = View::make('invoices/_show', ['invoice'=>$invoice])->render();
		$pdf->generateFromHtml($view, $path);
	    */
	    
		return $path;
	
	}
	
	/*
	public function handle(){
		
		$stateTypes = [3,6,10];
		$countyType = [4];
		$districtType = [7];
		
		$start = Carbon::today()->startOfDay()->startOfMonth()->subMonth();
		$end = Carbon::today()->endOfDay()->endOfMonth()->subMonth();
		
		$startDate = $start->setTimezone('UTC');
		$endDate = $end->setTimezone('UTC');
				 
		$checks = Check::where('created_at', '>=' , $startDate)->where('created_at', '<', $endDate)->get();
		
		
		$allChecks = [];
		$dupChecks = [];
		
		foreach($checks as $check){
			
			$order = "";
			
			if($check->order){
				$order = $check->order;
			}
			
			$name = $order->first_name . " " . $order->middle_name . $order->last_name;
			
			$companyId = $order->company_id;
			$company = Company::where('company_id', $companyId)->first();
			$companyName = $company->company_name;
			$companyName = strtolower(str_replace(".", "", $companyName));
			$companyName = preg_replace("/[^a-zA-Z]/", "", $company->company_name);
			$amount = $check->amount;
			
			if(!in_array($name, $allChecks)){
				$allChecks[] = $name;
			}elseif(!in_array($name, $dupChecks)){
				$dupChecks[] = $name;
			}
			
			$fh = fopen( public_path("dupChecks/checks1.log"), "a");
			
			foreach($dupChecks as $dup){
				
				$s = $order->created_at . " | " . $name . " | " . $check->id . " | " . $check->original_id . " | " . $amount . " | " . $companyName;
				fwrite($fh, $s);
			}
			
			fclose($fh);
			
		}

		//
				
	}
	 * 
	 */

}