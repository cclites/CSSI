<?php

namespace App\Http\Controllers\Library\Api;

//system
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

//models
use App\Models\Adjustment;
use App\Models\Check;
use App\Models\Invoice;
use App\Models\Minimum;
use App\Models\Transaction;

use Notification;
use \App\Notifications\InvoiceNotifyEmail;
use \App\Recipients\InvoiceRecipient;

//facades
use Log;
use Auth;
use DB;
use View;
use App;

use \Carbon\Carbon;

use Knp\Snappy\Pdf;
//use PDF;

class InvoicesLibrary{
	
	public function __construct()
    {
		//require_once('../vendor/autoload.php');
		//require_once('../../../../vendor/autoload.php');
		//require_once( base_path("/vendor/autoload.php") );
    }
	
	public function generateInvoicePath($companyId, $invoiceDate = null, $invoiceId = null){
		
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
			
		return public_path("pdf/" . $genDate .  $companyId . ".pdf");
		
	}
	
	public function getTransactions($companyId, $invoiceId = null, $getNulls = false ){
		
		if($invoiceId){
			return Transaction::where('invoice_id', $invoiceId)->get();
		}else{
			
			//REMINDER!! THESE TRANSACTIONS ARE FOR TESTING
			/*
			$transactions = Transaction::where('parent_id', $companyId)
							->whereBetween('created_at', [
				                Carbon::now()->startOfMonth()->startOfDay(),
								Carbon::now()->endOfMonth()->endOfDay()
				            ])
							//->where('amount', '>', 0)
							->whereNull('invoice_id')
				            ->orderBy('id', 'desc')->get();
			
			*/
			
			$transactions = Transaction::where('parent_id', $companyId)
							->whereBetween('created_at', [
				                Carbon::now()->startOfMonth()->subMonth()->startOfDay(),
								Carbon::now()->subMonth()->endOfMonth()->endOfDay()
				            ])
							//->where('amount', '>', 0)
							->whereNull('invoice_id')
				            ->orderBy('id', 'desc')->get();
			 
			/*			
			if(!$getNulls){
				$transactions = $transactions->whereNull('invoice_id');
			}
			*/
			
			return $transactions;
			
		}
		
	}
	
	public function applyAdjustments(&$invoice, $applyAdjustment = true){
		
		$adj = Adjustment::where("company_id", $invoice->user->company_id)->where("amount", ">", 0)->get();
		$adjustmentApplied = 0;
		$adjustmentAmount = 0;
		
		//In theory, should never get here if $adj is null
		if(!is_null($adj)){
			$adjustmentAmount = $adj->sum('amount');
		}

		if($adjustmentAmount > 0){
				
			$message = "Processing adjustments\n";			
		    cLog($message, 'app/commands', 'testInvoice');
			
			if($invoice->amount >= $adjustmentAmount ){		
				$adjustmentApplied = $adjustmentAmount;
				$invoice->amount -= $adjustmentAmount;
			}else if ($invoice->amount < $adjustmentAmount){
				$adjustmentApplied = $invoice->amount;
				$adjustmentAmount -= $invoice->amount;
				$invoice->amount = 0;		
			}
			
			$invoice->adjustment = $adjustmentApplied;
				
			foreach($adj as $adjustment){
				$adjAmount = $adjustment->amount;
				
				if($adjAmount <= $adjustmentApplied){
					$adjustmentApplied -= $adjAmount;
					$adjustment->amount = 0;
				}else if($adjAmount > $adjustmentApplied){
					$adjustment->amount -= $adjustmentApplied;
					$adjustmentApplied = 0;
				}else{
					//swallow
				}
				
				if($applyAdjustment){
					$adjustment->save();
				}
					
			}
				
		}

		return;
	}

    public function applyMinimums(&$invoice){
    	
    	$min = Minimum::where("company_id", $invoice->user->company_id)->first();
			
		if(isset($min)){
			$invoice->minimum = floatval($min->amount);
		}else{
			return;
		}

		if($invoice->minimum != 0 && $minimum <= $invoice->amount){
			$invoice->amount -= $invoice->minimum;
		}else{
			$invoice->amount = 0;
		}
		
		return;
    }

	public function renderInvoice($invoice, $path){
				
		if(is_file($path)){
			unlink($path);
		}
		
		$pdf = App::make('snappy.pdf.wrapper');
		$view = View::make('invoices/show', ['invoice' => $invoice]);
        $invoiceView = $view->render();
		$pdf->generateFromHtml($view, $path);
		
		return 0;	
	}
	
	
	public function streamInvoice($invoice){
		
		Log::info("InvoicesLibrary::streamInvoice");
		
		$companyId = $invoice->company_id;
		$pdf = App::make('snappy.pdf.wrapper');
		
		//I need to get the company here
		//$company = \App\_Models\Company::where('id', $invoice->company_id)->first();
		$company = Company::where('id', $companyId)->first();
		
		$view = View::make('invoices/_pdf', ['invoice' => $invoice])->render();
		return $pdf->loadHTML($view)->setPaper('a4')->inline();
		
		/*	
		$pdf = App::make('snappy.pdf.wrapper');

		$view = View::make('invoices/_pdf', ['invoice' => $invoice])->render();
		
		return $pdf->loadHTML($view)->setPaper('a4')->inline();
		 */
		
	}
	
	
	public function sendInvoice($company, $invoice, $path){
		
		/*
		$recipients = [];

		if($company->owner()->invoice){
			$recipients = explode(",", $company->owner()->invoice);
		}else{
			$recipients[] = $company->owner()->email;
		}
		
		foreach($recipients as $r){
			
			$r = trim($r);
			
			$recipient = new InvoiceRecipient($r);
			$result = $recipient->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $path, $company->owner()->company_name));
			
			//echo "Result is $result\n";
		 
		}*/
		
		return 1;
		
		/*
		if($company->owner()->invoice){
				
			cLog("**** Has invoice emails", 'app/v1/controllers', 'invoice');
			
				
			$recipients = explode(",", $company->owner()->invoice);

			foreach($recipients as $r){
				$r = trim($r);
				
				try{
					$recipient = new InvoiceRecipient($r);
					$result = $recipient->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $path, $company->owner()->company_name));
					
					$message = "Mailed invoice to recipient";			
		            cLog($message, 'app/v1/controllers', 'invoice');
					
					return $result;
					
				}catch(\Exception $e){
					
					$message = "Unable to mail invoice for recipients at " . $company->owner()->company_name;			
					cLog($message, 'app/v1/controllers', 'invoice');
					
				}
						
			}
	
		}else{
			
			cLog("**** Has no invoice emails", 'app/v1/controllers', 'invoice');
			
			try{
				
				$result = $company->owner()->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $path, $company->owner()->company_name));
				
				$message = "Mailed invoice to owner";			
		        cLog($message, 'app/v1/controllers', 'invoice');
				
				return $result;
				
			}catch(\Exception $e){
					
				$message = "Unable to mail invoice for owner at " . $company->owner()->company_name;			
				cLog($message, 'app/v1/controllers', 'invoice');
			}
	
		}
		 * 
		 */
		
		return null;
	}

	public function regenerateInvoice($invoiceId){
		
		
		if(!$invoiceId){
			return json_encode(["error"=>"An invoice ID is required."]);
		}
		
		$invoice = Invoice::find($invoiceId);
		
		if(!$invoice){
			return json_encode(["error"=>"The invoice id is invalid"]);
		}
		
		$companyId = $invoice->user->company_id;
		$company = new \App\Models\Company;
		$company->company_id = $companyId;
		
		$transactions = $this->getTransactions($companyId, $invoiceId, true);

		if (!$transactions->count()) {  
            return json_encode(["error"=>"This invoice has no transactions."]);
        }

		$invoice->amount = $transactions->sum('amount');
		$invoice->save();
		
		return json_encode(["message"=>"Invoice $invoiceId has been recreated."]);
		
	}
	
}