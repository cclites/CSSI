<?php

namespace App\Console\Commands\V1;

use Illuminate\Console\Command;

// Models
use App\Models\User;
//use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Adjustment;
use App\Models\Minimum;
use App\Models\Stat;

// _Models
use App\_Models\Check;
use App\_Models\Company;
use App\_Models\Invoice;
use App\_Models\Order;

// Notifications
use Notification;
use \App\Notifications\InvoiceNotifyEmail;
use \App\Recipients\InvoiceRecipient;


use Carbon\Carbon;

use Exception;
use Log;
use App;
use View;
use DB;

use Knp\Snappy\Pdf;

class _Invoice extends Command
{

    protected $signature = '_invoice';
    protected $description = 'Loop through orders to generate monthly invoices.';
	protected $stat;
	protected $il;
	protected $orderData;
	protected $month = 'March';
	protected $testing = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

		require_once( base_path('vendor/autoload.php') );
    }
	
	public function handle(){
		
		echo "Running _Invoice test\n";
		
		//**********************************************************
		//Delete all of the previous files
		
		/*
		$files = glob(public_path("pdf/" . $this->month ."_2019/*")); // get all file names
		
		foreach($files as $file){ // iterate files
		  if(is_file($file))
		    unlink($file); // delete file
		}
		*/
		
		//**********************************************************
		
		//$this->il = new App\Http\Controllers\Library\Api\InvoicesLibrary;
		//$il = $this->il;
		
		//$this->stat = Stat::where('name', 'LastRanInvoicing')->first();
		//$stat = $this->stat;

		//$lastUpdated = new Carbon($stat->updated_at);
		//$lastUpdatedDate = $lastUpdated->format("Ym");
		$curDate = date("Ym");
		
		//if($curDate == $lastUpdatedDate){
			//echo "Invoicing has already been completed for this month.\n";
			//die();
		//}
		

        //getting all of the companies
		$companies = Company::all();
		//$companies = Company::where('company_id', 'sXY2ij')->get();
		
		echo "Running companies.\n";

		//Wilmington  OTOwil
		//Cramer Investigative  aeCqwu
		//Ray Cammack studios GfN3Wa

		/* LAST MONTH - normal ops */
		
		//As of April---
		// ->subMonths(1) March
		// ->subMonths(2) February
		// ->subMonths(3) January
		
		
		//
		
		//Running april
		//running march
		//running february
		
		/******  DATES ********/

		//$start = Carbon::now()->startOfDay()->subMonth()->startOfMonth();
		//$end = Carbon::now()->endOfDay()->subMonth()->endOfMonth();
		
		$start = Carbon::now()->startOfDay()->subMonths(4)->startOfMonth();
		$end = Carbon::now()->endOfDay()->subMonths(4)->endOfMonth();
		$date = Carbon::now()->startOfDay()->subMonths(3)->startOfMonth()->format('Y-m-d');

		$startDate = $start->setTimezone('UTC');
		$endDate = $end->setTimezone('UTC');
		
		echo "Start date is $startDate\n";
		echo "End date is $endDate\n";
		echo "Date is $date\n";
		

		foreach($companies as $company){

			$orders = Order::where('company_id', $company->company_id)
					  ->where('created_at', ">=", $startDate)
					  ->where('created_at', "<=", $endDate)
					  ->whereNull('invoice_id')
					  //->where('invoice_id', 0)  //restore this for next month
					  //->take(3)
					  ->get();
					  
			if( $orders->count() < 1){
				//echo "No orders\n";
				continue;
			}else{
				//echo "Company has orders.\n";
			}

			$invoice = new Invoice;
			$invoice->date = $date;
            $invoice->company_id = $company->id;
			$invoice->notes = "REGENERATED";
			$invoice->save();

			
			$totalInvoiceAmount = 0;
					
			foreach($orders as $order){
				
				$checks = Check::where('order_id', $order->id)->get();
					  
				if( $checks->count() < 1){
					echo "No checks\n";
					continue;
				}else{
					//echo "Order has checks.\n";
				}

				$orderAmount = $checks->sum('amount');
				$totalInvoiceAmount += $orderAmount;
				
				$orderObj = [];
				$orderObj["amount"] = $orderAmount;
				$orderObj['orderDate'] = $order->created_at;
				$orderObj['details'] = $this->createOrderData($order, $checks);
				$this->orderData[] = $orderObj;
				
				$totalInvoiceAmount += $checks->sum('amount');
				$invoice->amount += $orderAmount;

				$order->invoice_id = $invoice->id;
				$order->save();
				 

			}
			
			
			try{
				//$il->applyAdjustments($invoice);	
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY ADJUSTMENTS  *************\n";
				//echo $message . "\n";
				sleep(30);
				continue;
			}
			
			try{
				//$il->applyMinimums($invoice);
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY MINIMUMS  *************\n";
				//echo $message . "\n";
				sleep(30);
				continue;
			}
			
			$invoice->save();
		    //$invoice->orders = $this->orderData;
			//$path = "";
			
			/*
			try{
				echo "Render Invoice\n";
				
				//$path = $this->renderInvoice($invoice);
				
				$renderedInvoice = $this->streamInvoice($invoice);
				
			}catch(\Exception $e){
				$message = "******** UNABLE TO GENERATE ATTACHMENT  *************\n";
				cLog($message, 'app/commands', 'invoice');
				cLog($e->getMessage(), 'app/commands', 'invoice');
				echo "$message\n";
				sleep(3);
				continue;
			}
			
			$this->orderData = [];
			
			try{
                echo "Send invoice\n";
				//$this->sendInvoice($company, $invoice, $path);

				
			}catch(\Exception $e){
				$message = "******** FAILED SENDING INVOICE *************\n";	
				echo "$message\n";
				sleep(1);
				continue;
			}
			 * 
			 */
			
		}//end foreach companies
		
		
	}//end handle
	
	public function createOrderData($order, $checks){
		
		$name = $order->first_name . " " . $order->last_name;
		$orderId = $order->id;
		
		$notes = "Order id: ($orderId) $name, ";
		
		foreach($checks as $check){
				
			$type = cache('types')->where('id', $check->type)->first();

			$title = $type->title;
			$amount = $check->amount;
			$typeId = $check->type;
			
			if($typeId == 3){ //state
			
				$state = json_decode($check->json_data);
				//$state = $json;
			    $title .= " (" . $state->code . ")" . " " . $amount;
			    
			}elseif($typeId == 4){ //county
			
				$county = json_decode($check->json_data);
				//$county = $json;
				$title .= " ( " . $county->title . " " . $county->state_code . " )" . " " . $amount;
				
				
			}elseif($typeId == 6){ //state
			
				$state = json_decode($check->json_data);
				//$state = $json;
				$title .= " (" . $state->code . ")" . " " . $amount;
				
			}elseif($typeId == 7){  //district
			
				$district = json_decode($check->json_data);
				//$district = $json;
				$title .= " ( " . $district->title . " " . $district->state_code . " )" . " " . $amount;
				
			}elseif($typeId == 10){  //mvr
			
				$state = json_decode($check->json_data);
			    //$state = $json;
				$title .= " (" . $state->code . ")" . " " . $amount;
				
			}else{
				$title .= " $amount";
			}
			
			$notes .= $title . ", ";	
		}
		
		return $notes;
	}
	
	public function streamInvoice($invoice){
		
		$companyId = $invoice->company_id;
		$pdf = App::make('snappy.pdf.wrapper');
		
		//I need to get the company here
		$company = Company::where('id', $companyId)->first();
		
		$view = View::make('invoices/_show', ['invoice' => $invoice, 'company' => $company])->render();
		return $pdf->loadHTML($view)->setPaper('a4')->inline();
		
	}
	
	public function renderInvoice($invoice){
		
		$companyId = $invoice->company_id;
		//echo "Rendering Invoice # " . $invoice->id . "\n";
		
		$company = Company::where('id', $companyId)->first();
		$nonce = createSeed(4);
		
		$genDate = \Carbon\Carbon::today()->format("Y_m");
		$path = public_path("pdf/" . $this->month ."_2019/" . $genDate . "_" . $company->company_id . "_" . $nonce . ".pdf");
		

		$pdf = App::make('snappy.pdf.wrapper');
		$view = View::make('invoices/_show', ['invoice'=>$invoice])->render();
		$pdf->generateFromHtml($view, $path);
	
		return $path;
	
	}
	
	public function sendInvoice($company, $invoice, $path){
		
		$recipients = [];
		
		echo "SENDING INVOICES FOR " . $company->company_name . "\n";

		if($company->invoice_recipients){
			$recipients = explode(",", $company->invoice_recipients);
		}else{
			$recipients[] = $company->email;
		}
		
		foreach($recipients as $r){
			
			$r = trim($r);
			echo "-----  Recipient:  $r\n";
			
			$recipient = new InvoiceRecipient($r);
			$result = $recipient->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $path, $company->company_name));
			echo json_encode($result) . "\n";
		}
		
		return 1;
	}

}