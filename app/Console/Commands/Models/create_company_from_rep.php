<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* _Models  */
use App\_Models\Company;

/* Models */
use App\Models\User;

/* Facades */
use Carbon\Carbon;
use DB;
use Log;

class create_company_from_rep extends Command
{
   	protected $signature = 'create_company_from_rep';
	protected $description = 'convert company rep to company';
	
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	
		$companyReps = User::where('company_rep', true)->get();
		
		echo $companyReps->count() . "\n";
		
		foreach($companyReps as $rep){
			
			$c = new Company;
			
			$c->whitelabel_id = $rep->whitelabel_id;
			$c->key = $rep->key;
			$c->address = $rep->address;
			$c->secondary_address = $rep->secondary_address;
			$c->city = $rep->city;
			$c->state = $rep->state;
			$c->email = $rep->email;
			$c->zip = $rep->zip;
			$c->country = $rep->country;
			$c->phone = $rep->phone;
			$c->website = $rep->website;
			$c->is_approved = $rep->is_approved;
			$c->is_setup_contact = $rep->is_setup_contact;
			$c->is_suspended = $rep->is_suspended;
			
			$c->created_at = $rep->created_at;
			$c->updated_at = $rep->updated_at;
			
			$c->stripe_customer_id = $rep->stripe_customer_id;
			$c->card_brand = $rep->card_brand;
			$c->card_last_four = $rep->card_last_four;
			$c->card_expiration = $rep->card_expiration;
			
			$c->company_id = $rep->company_id;
			$c->sandbox = $rep->sandbox;
			$c->company_rep = $rep->id;
			
			$c->company_name = $rep->company_name;
			$c->invoice_recipients = $rep->invoice;
			
			$c->cell_phone = $rep->cell_phone;
			$c->extension = $rep->extension;
			$c->is_app = $rep->is_app;
			$c->device = $rep->device;
			
			$c->save();
			
		}
    	
    }

}