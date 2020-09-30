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
use DB;

class Bill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill {--simulate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loop through all users/checks and create transaction for each one that hasn\'t yet been billed.';
	protected $stat;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
    	
		$this->stat = Stat::where('name', 'LastRanBilling')->first();
		
    	//Log::info("Billing");
		
        $this->info($this->description);
		
		$stat = $this->stat;
		$stat->val = "Processing transactions";
		$stat->save();
        
        $users = User::orderBy('id')
        ->with('prices')
        ->chunk(100, function ($users) {
        	
            foreach ($users as $user) {
            	
                if ($user->is_suspended) {
                	//Log::info('User ID: '.$user->id.' - Skipping suspended account');
                    //$this->line('User ID: '.$user->id.' - Skipping suspended account');
                    continue;
                }
				
                $checks = Check::where('user_id', $user->id)
                    ->whereNull('transaction_id')
                    ->whereNotNull('completed_at')
					->where('sandbox', false)
                    ->with('types')
                    ->get();
					
				$this->line('User ID: '.$user->id.' - has '.$checks->count().' unbilled checks');

                foreach ($checks as $check) {
                	
                    $description = 'Check (ID: '.$check->id.') for '.$check->full_name . "\n";
	
                    $check_amount = 0;
					$type_ids = "";
					
					
					//there is no check_type record maybe?
					
					//echo json_encode($check->checktypes);
					
					if(!empty($check->checktypes)){
						
						foreach($check->checktypes as $type) {
	                    	
							$this->line("TYPE ID: " . $type->type_id);
							//$this->line("OWNER ID: " . $type->ownedBy());
							
							$owner_id = $type->ownedBy();
	
							if($type->type_id == 3){ //federal state
								
								$states = $type->check->states;
								
								foreach ($states as $state){
	
									$stateCode = "  (" . $state->code. ")";
									$statePrice = DB::table("prices")
							 					  ->where("prices.user_id", $owner_id)
							 					  ->where("type_id", $type->type_id)
							 					  ->pluck("amount");
	
									$cost = $statePrice[0] + $state->extra_cost;	  
									$checkPrice = displayMoney($cost);	
									$check_amount += $cost;
									
									$description .= "\n" . $checkPrice." ".$type->type->title .  $stateCode;
	
								}
	
							}elseif($type->type_id == 4){  //county 
							
								$counties = $type->check->counties;
								
								$countyPrice = DB::table("prices")
											   ->where("prices.user_id", $owner_id)
											   ->where("type_id", $type->type_id)
											   ->pluck("amount");
											   
								$baseCost = $countyPrice[0];
											   
								foreach($counties as $county){
									
									//echo json_encode($county) . "\n";
									//echo "Extra cost " . $county->extra_cost . "\n";
									
									$passthru = $county->extra_cost;
									$cost = $passthru + $baseCost;
									$check_amount += $cost;
									
									//I need state and county
									$description .= displayMoney($cost). " " .$type->type->title . "(" . $county->state_code . " - " . $county->title . ")";
								}
								
								$checkPrice = displayMoney($cost);		
								
							}elseif($type->type_id == 6){
								
								$price = DB::table("prices")
											   ->where("prices.user_id", $owner_id)
											   ->where("type_id", $type->type_id)
											   ->pluck("amount");
											   
								$amount = $price[0];
								
								$states = $type->check->federal_states;
								//$states = $type->check->check_states;
								
								
								
								foreach($states as $state){
									
									$check_amount += $amount;
									
									$oTitle = $description;
									$stateId = $state->state_id;
									
									$stateCode = "  (" . $state->code . ")";
									$oTitle .= $stateCode;
									
									$description .= displayMoney($amount). " " . $oTitle;
								}		   
								
							}elseif($type->type_id == 7){
								
								$price = DB::table("prices")
											   ->where("prices.user_id", $owner_id)
											   ->where("type_id", $type->type_id)
											   ->pluck("amount");
											   
								$amount = $price[0];
								
								$districts = $type->check->districts;
								
								foreach($districts as $district){
									
									$check_amount += $amount;
									
									$stateCode = "  (" . $district->state_code . ")";
								    $description .= displayMoney($amount). " " . $district->title .  $stateCode;
								}
								
							}elseif($type->type_id == 10){
								
								$price = DB::table("prices")
											   ->where("prices.user_id", $owner_id)
											   ->where("type_id", $type->type_id)
											   ->pluck("amount");
								
								$basePrice = $price[0];
								$passthru = $type->check->states[0]->mvr_cost;
								$cost = $basePrice + $passthru;
								$check_amount += $cost;
								
								//\Carbon\Carbon::now('UTC')
								
								$cutoffDate = Carbon::now('UTC');
								//At the end of the month, this needs to switch over to using check->states_mvr
								
								$stateCode = "  (" . $type->check->states[0]->code. ")";
								
								/*
								if ($cutoffDate < "2019-01-00 00:00:00") {
									$stateCode = "  (" . $type->check->states[0]->code. ")";
								}else{
									$stateCode = "  (" . $type->check->mvr_states[0]->code. ")";
								}
								 * 
								 */
	
								$description .= displayMoney($cost). " " .$type->type->title .  $stateCode;
								 
							}else{
								
								$price = DB::table("prices")
											   ->where("prices.user_id", $owner_id)
											   ->where("type_id", $type->type_id)
											   ->pluck("amount");
									
								$check_amount += $price[0];		   
								$description .= displayMoney($price[0]). " " .$type->type->title;
								
							}
	
							$temp = $type->type_id . ",";
							$type_ids .= $temp;
	
							$type->is_completed();
	                        
		 
	                    } // end foreach types
						
						
					}
					
	                    
                    
                   // $this->line("CHECK AMOUNT: " . $check_amount);
                    //$this->line("DESCRIPTION: " . $description);
                    
                    
                    $transaction = new Transaction;
                    $transaction->user_id = $check->user_id;
					$transaction->parent_id = $check->company_id;
                    $transaction->check_id = $check->id;
                    $transaction->date = $check->completed_at;
                    $transaction->amount = $check_amount;
                    $transaction->description = $description;
					$transaction->check_type = $type_ids;
					$transaction->notes = 'DEV';

                    $transaction->save();
                    
					//DB::statement('SET FOREIGN_KEY_CHECKS=0');
					
                    $check->transaction_id = $transaction->id;
                    $check->save();
					
					//$this->info('');
        			//$this->info('');

                } // end foreach checks

                
            } // end foreach user
        });

        $this->info('Billing complete');
        $this->info('');
        $this->info('');
		
		$stat->val = "Ready";
		$stat->save();
    }
}