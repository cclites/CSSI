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

class TestGenerateInvoices extends Command
{

    protected $signature = 'testGenerateInvoices {invoiceId?}';
    protected $description = 'Testing invoicing generation';
	
	//SAMPLE: sudo php artisan testGenerateInvoices 46965
	
	protected $stat;
	protected $il;
	protected $invoiceId;

    public function __construct()
    {
        parent::__construct();
		require_once(__DIR__ . '/../../../vendor/autoload.php');	
    }
	
	
	
	public function handle(){
		
		if( !is_null($this->argument('invoiceId')) ){
			echo "There is an invoiceID\n";
			$this->invoiceId = $this->argument('invoiceId');
		}else{
			echo "There is no invoiceID\n";
			$this->invoiceId = 0;
		}
		
		$invoiceId = $this->invoiceId;
		
		$this->il = new \App\Http\Controllers\Library\Api\InvoicesLibrary;
		$il = $this->il;
		
		$this->stat = Stat::where('name', 'InvoiceTesting')->first();
		$stat = $this->stat;
		//I want last updated - 
		
		$lastUpdated = new Carbon($stat->updated_at);
		$lastUpdatedDate = $lastUpdated->format("Ym");
		$curDate = date("Ym");

		if($curDate == $lastUpdatedDate){
			$stat->val = "Test Invoicing completed this month. Continuing with testing.";
		    $stat->save();
			//return;
			echo "Skipping run-date check\n";
		}
		

		//if invoice id is set, get the invoice. From that, get company id, then get company.
		if($invoiceId){
			
			echo "Finding the invoice and company.\n";
			
			$invoice = Invoice::find($invoiceId);

			if(!$invoice){
				$stat->val = "Unable to retrieve test invoice.";
		        $stat->save();
				return;
			}
			
			$companyId = $invoice->user->company_id;
			$comp = new \App\Models\Company;
			$comp->company_id = $companyId;
			$invoiceDate = $invoice->date;
			
		    $companies[] = $comp;

		}else{
			
			echo "Getting all companies\n";
			$companies = \App\Models\Company::companies();
			$invoiceDate = null;
		}
		
		foreach($companies as $company){
			
			$stat->val = "\n**************************************************************";
			$stat->val = "Processing test invoice for " . $company->owner()->company_name;
		    $stat->save();
			
			$transactions = [];
			$path = "";
			
			$message = "Processing test invoice for " . $company->owner()->company_name . "\n";		
			cLog($message, 'app/commands', 'testInvoice');
			
			try{
				
				if($invoiceId){
					echo "Get transactions for invoice\n";
					$path = $il->generateInvoicePath($company->company_id, $invoiceDate);
					$transactions = $il->getTransactions($company->company_id, $invoiceId, true);
				}else{
					echo "Get transactions for company\n";
					$path = $il->generateInvoicePath($company->company_id);
					$transactions = $il->getTransactions($company->company_id, null, true);
				}
				
			}catch(\Exception $e){
				
				$message = "******** UNABLE TO GET PATH OR TRANSACTIONS FOR COMPANY  *************";
				cLog($message, 'app/commands', 'testInvoice');
				
				cLog($e->getMessage(), 'app/commands', 'testInvoice');
				echo "$message\n";
				continue;
			}
			
			if(is_file($path)){
				unlink($path);
			}

			if (!$transactions->count()) {

				$message = "No transactions for this user.\n";			
			    cLog($message, 'app/commands', 'testInvoice');
				echo "$message\n";
				die("$message\n");
                continue;
            }else{
            	//echo json_encode($transactions) . "\n";
            	//echo "I have transactions\n";
            }
			
			//die("I have transactions\n");
			
			$message = "Creating a new invoice\n";			
			cLog($message, 'app/commands', 'testInvoice');
			echo "$message\n";
			
			if($invoiceId){
				$invoice = Invoice::find($invoiceId);
				$invoice->id = $invoiceId;
			}else{
				$invoice = new Invoice;
	            $invoice->user_id = $company->id()[0];
	            $invoice->date = Carbon::now();
	            $invoice->amount = $transactions->sum('amount');
				$invoice->id = 999999;
			}
			
			if(!$invoice){
				$message = "******** NO INVOICE AVAILABLE  *************";
				cLog($message, 'app/commands', 'testInvoice');
				continue;
			}else{
				echo "I have an invoice.\n";
			}
			
			
			$invoice->transactions = $transactions;
			
			//$transactions->update(['testing' => true]);
			foreach($transactions as $transaction){
				$transaction->testing = true;
				$transaction->save();
			}
			
			//die("Do only one company.\n");
			//continue;
			
			try{
				echo "Applying adjustments.\n";
				//$il->applyAdjustments($invoice, false);
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY ADJUSTMENTS  *************";
				cLog($message, 'app/commands', 'testInvoice');
				
				echo "$message\n";
				
				cLog($e->getMessage(), 'app/commands', 'testInvoice');
				continue;
			}
			
			try{
				echo "Applying minimums.\n";
				//$il->applyMinimums($invoice);
			}catch(\Exception $e){
				$message = "******** UNABLE TO APPLY MINIMUMS  *************";
				cLog($message, 'app/commands', 'testInvoice');
				cLog($e->getMessage(), 'app/commands', 'testInvoice');
				continue;
			}

			try{
				
				//echo json_encode($invoice->transactions) . "\n";

				$il->renderInvoice($invoice, $path);
				
				$message = "Generated Attachment.\n";			
			    cLog($message, 'app/commands', 'testInvoice');
				echo "$message\n";
				
			}catch(\Exception $e){
				$message = "******** UNABLE TO GENERATE ATTACHMENT  *************";
				echo "$message\n";
				cLog($message, 'app/commands', 'testInvoice');
				cLog($e->getMessage(), 'app/commands', 'testInvoice');
				continue;
			}
			
			if($invoiceId){
			
				try{
					echo "Pretending to send the Invoice to " . $company->owner()->company_name . "\n";
					
					
					//this is a sanitary check. Should only get here when procewssing a single transaction.
					if(count($companies) == 1){
						echo "Sending invoice...        ";
						//$result = $il->sendInvoice($company, $invoice, $path);
					}
					
					$message = "Invoice has been sent.\n";			
				    cLog($message, 'app/commands', 'testInvoice');
					echo "$message\n";
	
				}catch(\Exception $e){
					echo "Failed sending the invoice?.\n";
					$message = "******** FAILED SENDING INVOICE *************";
					cLog($message, 'app/commands', 'testInvoice');
					echo "$message\n";
					cLog($e->getMessage(), 'app/commands', 'testInvoice');
				}
			}else{
				echo "No invoice id. Not mailing invoice.\n";
			}
			
		
		
			if(is_file($path)){
				//unlink($path);
			}
			
			foreach($transactions as $transaction){
				$transaction->testing = false;
				$transaction->save();
			}
			
			//die("Done processing " . $company->company_name . "\n");
			
		}

		if(!$invoiceId){
			//$this->checkOrphanedTransactions();
		}
		
		/*
		Transaction::where("testing", true)
				   ->update(["testing"=>false]);
		 * 
		 */
		
        $message = "Test invoice generation complete\n";			
		cLog($message, 'app/commands', 'testInvoice');
		
		$stat->val = "Testing complete.";
		$stat->save();
	
	}//end handle
	
	public function checkOrphanedTransactions(){
		
		$stat = $this->stat;
		$stat->val = "Checking for orphaned transactions.";
		$stat->save();
		
		$orphans = Transaction::whereNull('invoice_id')->where('testing', false)->get();
		
		if($orphans->count()){
			
			$message = "Orphans found\n";
			cLog($message, 'app/commands', 'testInvoice');
			
			$message = json_encode($orphans);
			cLog($message, 'app/commands', 'testInvoice');
		}else{
			$message = "No orphans found\n";
			cLog($message, 'app/commands', 'testInvoice');
		}
		
		//cLog($message, 'app/commands', 'testInvoice');
		
	}

}