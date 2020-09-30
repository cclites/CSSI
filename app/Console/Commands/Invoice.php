<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Adjustment;
use App\Models\Minimum;
use App\Models\Stat;

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

class GenerateInvoices extends Command
{

    protected $signature = 'invoice';
    protected $description = 'Loop through all users and create invoices once a month.';
	protected $stat;
	protected $il;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function handle(){
		
		die();
		//die("Do not run again this month.");
		
		$this->il = new App\Http\Controllers\Library\Api\InvoicesLibrary;
		$il = $this->il;
		
		$this->stat = Stat::where('name', 'LastRanInvoicing')->first();
		$stat = $this->stat;
		
		$lastUpdated = new Carbon($stat->updated_at);
		
		$lastUpdatedDate = $lastUpdated->format("Ym");
		
		//$curDate = date("Ym");
		
		//Carbon::now('UTC');
		
		$curDate = '201901';

        
		if($curDate == $lastUpdatedDate){
			echo "Invoicing has already been completed for this month.\n";
			//return;
		}
		 

        //getting all of the companies
		$companies = \App\Models\Company::companies();
		
		foreach($companies as $company){
			
		    $stat->val = "Processing test invoice for " . $company->owner()->company_name;
		    $stat->save();

			$message = "Processing test invoice for " . $company->owner()->company_name;		
			cLog($message, 'app/commands', 'invoice');
			
			echo "$message\n";
			
			$path = "";
			
			try{
				$path = $il->generatePath($company->company_id);
			}catch(\Exception $e){
				$message = "******** UNABLE TO GET PATH FOR COMPANY  *************";
				cLog($message, 'app/commands', 'invoice');
				cLog($e->getMessage(), 'app/commands', 'invoice');
				echo "$message\n";
				sleep(30);
				continue;
			}
				
			
			if(file_exists($path)){
				cLog("Invoice already exists for this user. Skipping.......", 'app/commands', 'testInvoice');
				$this->info("Invoice already exists for this user. Skipping.......");
				
				echo "Invoice already exists for this user. Skipping.......\n";
				sleep(30);
				continue;
			}
			
			$message = "*****************************************\n";
			$message .= "Checking company " . $company->company_id ."\n";			
			cLog($message, 'app/commands', 'invoice');

			
			try{
				
				//$transactions = $il->getTransactions($company->owner()->company_id);
				
				/*
				$transactions = Transaction::where('parent_id', $company->owner()->company_id)
								->whereNull('invoice_id')
					            ->orderBy('id', 'desc')->get();
				 * */
				 
				 /*
				 $transactions = Transaction::where('parent_id', $company->owner()->company_id)
							->whereBetween('created_at', [
				                Carbon::now()->startOfMonth()->subMonth()->startOfDay(),
								Carbon::now()->subMonth()->endOfMonth()->endOfDay()
				            ])
							->whereNull('invoice_id')
				            ->orderBy('id', 'desc')->get();
				  */
				  
				  $transactions = Transaction::where('parent_id', $company->company_id)
				                  ->where('date', '>=', '2018-12-01')
								  ->where('date', '<=', '2018-12-31')
								  ->whereNull('invoice_id')
				                  ->orderBy('id', 'desc')->get();
											  
				  
				
			}catch(\Exception $e){
				$message = "******** UNABLE TO GET TRANSACTIONS FOR COMPANY  *************\n";
				cLog($message, 'app/commands', 'invoice');
				cLog($e->getMessage(), 'app/commands', 'invoice');
				echo "$message\n";
				sleep(30);
				continue;
			}
			 
				            
			//$message = "Retrieved Transactions";
			//cLog($message, 'app/commands', 'invoice');
			
			echo "Transactions amount = " . $transactions->sum('amount') . "\n";
			echo "Retrieved transactions\n";
			
			sleep(2);
			continue;
			
					
			if (!$transactions->count()) {
				//cLog("No transactions for this user.", 'app/commands', 'invoice');
				
				//echo "No transactions for this user.\n";
				
				//sleep(2);
                continue;
            }
			
			//echo "Log the transactions\n";
			//Log::info(json_encode($transactions));
			
			echo "Creating a new invoice for company " . $company->owner()->company_name . "\n";
			//$message = "Creating a new invoice for company " . $company->owner()->company_name;			
			//cLog($message, 'app/commands', 'invoice');
			
            $invoice = new Invoice;
            $invoice->user_id = $company->id()[0];
            $invoice->date = Carbon::now();
            $invoice->amount = $transactions->sum('amount');
			
			
			if(!$invoice){
				$message = "******** NO INVOICE CREATED  *************\n";
				cLog($message, 'app/commands', 'invoice');
				echo "$message\n";
				sleep(30);
				continue;
			}
			
			try{
				/*||      PRODUCTION CODE     ||*/
				//$il->applyAdjustments($invoice);
				/*||    					  ||*/
				
				//$il->applyAdjustments($invoice, false);
				
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY ADJUSTMENTS  *************\n";
				//cLog($message, 'app/commands', 'invoice');
				//cLog($e->getMessage(), 'app/commands', 'invoice');
				echo $message . "\n";
				sleep(30);
				continue;
			}
			
			try{
				//$il->applyMinimums($invoice);
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY MINIMUMS  *************\n";
				//cLog($message, 'app/commands', 'invoice');
				//cLog($e->getMessage(), 'app/commands', 'invoice');
				echo $message . "\n";
				sleep(30);
				continue;
			}
			
			/*||      TESTING CODE        ||*/
			/*
			foreach($transactions as $transaction){
				$transaction->testing = true;
				$transaction->save();
			}
			/*||                          ||*/

			$invoice->save();
			
			$invoice->transactions = $transactions;

			//$invoice->id=999999;
            
            /*||      PRODUCTION CODE     ||*/
			foreach($transactions as $transaction){
				$transaction->invoice_id = $invoice->id;
				$transaction->notes = $curDate;
				//$transaction->notes = "testing";
				$transaction->save();
			}
			/*||                             ||*/
			

			try{
				echo "Render Invoice\n";
				$il->renderInvoice($invoice, $path);
			}catch(\Exception $e){
				$message = "******** UNABLE TO GENERATE ATTACHMENT  *************\n";
				cLog($message, 'app/commands', 'invoice');
				cLog($e->getMessage(), 'app/commands', 'invoice');
				echo "$message\n";
				sleep(30);
				continue;
			}
			
			try{
				echo "Skip sending the Invoice\n";
				//$il->sendInvoice($company, $invoice, $path);
			}catch(\Exception $e){
				$message = "******** FAILED SENDING INVOICE *************\n";
				//cLog($message, 'app/commands', 'invoice');
				//cLog($e->getMessage(), 'app/commands', 'invoice');
				
				echo "$message\n";
				sleep(30);
			}

            if(is_file($path)){
				//unlink($path);
			}
            
			/*||      TESTING CODE        ||*/
			/*
			foreach($transactions as $transaction){
				$transaction->testing = false;
				$transaction->save();
			}
			/*||                          ||*/
			
			$message = "End create invoice for company " . $company->owner()->company_name . "\n";
			
			
			echo "$message\n";
				
			
			$message .' ******************************************************\n';			
			cLog($message, 'app/commands', 'invoice');
			cLog("\n", 'app/commands', 'invoice');
			
			//sleep(30);
			echo "Ending this company. Next.\n";
			sleep(2);
			//break;
			//die("Handled one company\n");
			
		}//end foreach companies
		
		cLog("Invoicing complete", 'app/commands', 'invoice');
		$stat->val = "Invoicing Complete.";
		$stat->save();
		
		echo "COMPLETED\n";
		
	}//end handle
	
	public function generatePath($companyId, $invoiceDate = null, $invoiceId = null){
		
		$company = Company::where('id', $companyId)->first();
		$nonce = createSeed(4);
		
		$genDate = \Carbon\Carbon::today()->format("Y_m");
		return public_path("_pdf/" . $this->month ."_2019/" . $genDate . "_" . $company->company_id . "_" . $nonce . ".pdf");
		
		/*
		if($invoiceDate){
			$iDate = new \Carbon\Carbon($invoiceDate);
			$genDate = $iDate->format("Y_m_");
		}elseif($invoiceId){
			$invoiceObj = Invoice::find($invoiceId);
			$iDate = new \Carbon\Carbon($invoiceObj->date);
			$genDate = $iDate->format("Y_m_");
		}else{
		   $genDate = date("Y_m_");
		}
		
		return public_path("_pdf/" . $genDate .  $companyId . ".pdf");
		 * 
		 */
		
	}

}