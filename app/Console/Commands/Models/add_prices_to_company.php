<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;

/* _Models  */
use App\_Models\Check;
use App\_Models\Company;
use App\_Models\Checktype;
use App\_Models\Price;


/* Facades */
use Carbon\Carbon;
use DB;
use Log;

class add_prices_to_company extends Command
{
   	protected $signature = 'add_prices_to_company';
	protected $description = 'add prices for company';
	
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
    	/*
    	$prices = DB::table("cssi.prices")->where('user_id', 3570)->get();
		
		foreach($prices as $price){
			
			$p = new Price;
			
			$p->company_id = 'P8UqQ4';
			$p->amount = $price->amount;
			$p->created_at = $price->created_at;
			$p->updated_at = $price->updated_at;
			$p->type_id = $price->type_id;
			$p->admin_id = 'eeeeee';
			
			$p->save();
			
		}
		 */
	
      //$companies = Company::all();
      $companies = Company::where('company_id', '00lXzu')->get();
	  
	  echo $companies->count() . "\n";
	  
	  foreach($companies as $company){
	  	
		$prices = DB::table("cssi.prices")->where('user_id', $company->company_rep)->get();
		  
		
		  
		foreach($prices as $price){
			
			$p = new \App\_Models\Price;
			
			$p->company_id = $company->company_id;
			$p->amount = $price->amount;
			$p->created_at = $price->created_at;
			$p->updated_at = $price->updated_at;
			$p->type_id = $price->type_id;
			$p->admin_id = 'eeeeee';
			
			echo json_encode($p) . "\n";
			
			
			
			echo $p->save();
			
		}
		
		
	  }

    }
	 
}

