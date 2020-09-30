<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Invoice;
use App\Models\Transaction;

// Notifications
use Notification;
use \App\Notifications\InvoiceNotifyEmail;
use \App\Recipients\InvoiceRecipient;

use Carbon\Carbon;

use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Error\ApiConnection;
use Stripe\Error\Base;
use Exception;
use Log;
//use \App\Http\Controllers\Api\V1\TransactionController;
use App;
use View;

use Knp\Snappy\Pdf;

class SendInvoice extends Command{
		
	protected $signature = 'send_invoice';
	protected $description = 'Manually send invoices.';
	
	 public function __construct()
    {
        parent::__construct();
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function handle(){
		
		/*
		$invoices = \App\_Models\Invoice::whereBetween('created_at', [
									Carbon::now()->startOfMonth(),
									Carbon::now()->endOfMonth()
								])
								//->where('user_id', 3155)
								->get();
        */
        
        $invoices = \App\_Models\Invoice::all();
        
		foreach($invoices as $invoice){
			
			echo "Invoice recipient(s) for " . $invoice->company->company_name . "\n"; 
			
			if($invoice->company->invoice_recipients){
				
				$recips = explode(",", $invoice->company->invoice_recipients);
				
				foreach($recips as $r){
					echo '     Send invoice to recipient: ' . $r . "\n";
				}
				
			}else{
				echo '     Send invoice to company order: ' . $invoice->company->email . "\n";
			}
			
			echo "\n\n";
			
			/*
			echo $invoice->user->company_id . "\n";
			$companyId = $invoice->user->company_id;

			$company = User::where("company_id", $companyId)->where('company_rep', true)->first();
			
			$path = public_path("pdf/" . "2018_12_" . $companyId . ".pdf");
			
			if(!file_exists($path)){
				continue;
			}
			
			$invoiceAttachment = $path;
			
			
			if($company->invoice){
				
				$this->info('Send invoice to recipient(s)');
				
				$recips = explode(",", $company->invoice);
				
				//still need to run this
				foreach($recips as $r){
					$recipient = new InvoiceRecipient($r);
					echo 'Send invoice to' . $r . "\n";
					//$recipient->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $invoiceAttachment, $company->company));
				}

			}else{
				$this->info('Unlinking file - already sent to owners');
				//unlink($path);
				//$company->notify(new InvoiceNotifyEmail($invoice->id, $invoice->amount, $invoiceAttachment, $company->company));
			}
			*/
			//sleep(1);
			//unlink the file
		}
	}
}