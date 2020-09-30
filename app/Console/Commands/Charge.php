<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Type;
use App\Models\Price;
use App\Models\Transaction;
use App\Models\Stat;

use Carbon\Carbon;
use Exception;
use Log;

// Notifications
use Notification;
use \App\Notifications\BilledEmail;
use \App\Recipients\InvoiceRecipient;


class Charge extends Command{
	
	protected $signature = 'charge';
	protected $description = 'Charge users who have payment details on file.';
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		//die("Cannot charge.");
		
		$this->stat = Stat::where('name', 'LastRanCharges')->first();
		$stat = $this->stat;
		
		$lastUpdated = new Carbon($stat->updated_at);
		$lastUpdatedDate = $lastUpdated->format("Ym");
		$curDate = date("Ym");

		if($curDate == $lastUpdatedDate){
			echo "Charges have already been completed for this month.\n";
			Log::info("Charges have already been completed for this month.");
			sleep(20);
			//return;
		}
		
		//Make sure that invoicing is complete
		$this->stat = Stat::where('name', 'LastRanInvoicing')->first();
		$stat = $this->stat;
		
		$lastUpdated = new Carbon($stat->updated_at);
		$lastUpdatedDate = $lastUpdated->format("Ym");
		$curDate = date("Ym");

		if($curDate !== $lastUpdatedDate){
			//echo "Invoicing has not been completed this month.\n";
			//sleep(20);
			//Log::info("Invoicing has not been completed for this month.");
			//return;
		}
		
		$invoices = \App\_Models\Invoice::where('created_at', '>', \Carbon\Carbon::now()->startOfMonth()->startOfDay())
		            ->where('completed', false)
		            ->with('company')
		            ->get();
		
		echo "Invoice count: " . $invoices->count() . "\n";
		
		$invoicesTotal = 0;
		$chargedTotal = 0;
		
		
			
		foreach($invoices as $invoice){
			
			$invoicesTotal += $invoice->amount - $invoice->adjustment;
			
			if(!$invoice->company->stripe_customer_id || $invoice->completed){
				continue;
			}
			
			$chargedTotal += $invoice->amount - $invoice->adjustment;

			if( $invoice->amount > 0 ){
				
				try{
					
					$totalAmount = $invoice->amount - $invoice->adjustment;

					$charge = \Stripe\Charge::create([
					    'amount' => ($totalAmount) * 100,
					    'currency' => 'usd',
					    'customer' => $invoice->company->stripe_customer_id,
					]);
					
					
					if($charge->outcome->type == "authorized"){
				    						
				 		$chargeInfo = [
							"Amount" => $charge->amount/100,
							"CompanyId" => $invoice->company_id,
							"Message" => $charge->outcome->seller_message,
							"Authorized" => $charge->outcome->type,
							"TransactionId" => $charge->balance_transaction
						];
						
						$this->info(json_encode($chargeInfo));
						$invoice->stripe_charge = json_encode($chargeInfo);
						$invoice->reconciled = true;
						$invoice->reconciled_by = "System";
						$invoice->reconciled_date = \Carbon\Carbon::now();
						$invoice->completed = true;
						$invoice->save();
						
				        echo "SENDING CHARGE NOTIFICATION FOR " . $invoice->company->company_name . "\n";
						
						$recipients = [];

						if($invoice->company->invoice_recipients){
							$recipients = explode(",", $invoice->company->invoice_recipients);
						}else{
							$recipients[] = $invoice->company->email;
						}

						foreach($recipients as $r){
							
							$r = trim($r);
			        		echo "-----  Recipient:  $r\n";
							
							$recipient = new InvoiceRecipient($r);
							$recipient->notify(new BilledEmail($totalAmount));
						}
						
					}else{
						Log::info("Unable to charge card.");
						Log::info(json_encode($charge));
					}
					
	
				}catch(\Exception $e){
					Log::info("Unable to charge company " . $invoice->company->company_name);	
				}
				
			}
					
		}

        echo "Invoices total: $invoicesTotal\n";
		echo "Charged total: $chargedTotal\n";

		echo "Complete\n";
		
	}
}
