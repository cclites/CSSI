<?php

// Note: this is a joining table with additional attributes stored in it. 
// Sometimes it's just easier to edit this table directly rather than go with Laravel's pivot functions

namespace App\Models;

use DB;
use Log;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Checktype extends Model {

	public $timestamps = false;

	protected $table = 'check_type';
	
	protected $ownerId = null;
	protected $priceArray = null;
	
	
	/*
	public function __construct()
    {
        $this->prices = Price::
    }
	*/

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }

    public function type()
    {
    	return $this->belongsTo('App\Models\Type');
    }

	public function is_completed(){
		Log::info("Setting Checktype completed");
		$this->completed_at = \Carbon\Carbon::now();
        //$this->save();
        return true;
	}
	
	public function ownedBy(){
		
		if(isset($this->ownerId)){
			//Log::info("Owned by is set");
			return $this->ownerId;
		}

		$userId = DB::table("check_type")
		           ->join("checks", "check_type.check_id", "=", "checks.id")
				   ->select("checks.user_id")
				   ->where("check_type.check_id", $this->check_id)
				   ->first();
		   
		$user = User::find($userId->user_id);
		
		$company_id = $user->company_id;

		$owner = DB::table("users")
		         ->where('company_rep', true)
				 ->where('company_id', $company_id)
				 ->first();

		if(!$owner){
			Log::info("There is no owner for this transaction");
			return 0;
		}

		$this->ownerId = $owner->id;
		return $this->ownerId;
				   
	}
	
	
	//TODO: Add pricing into CheckType model for education, employment, Infutor, and UsInfoSearch
	public function price(){
		
		if( is_null($this->priceArray) ){
			$this->priceArray = $this->prices();
		}
		
		//echo json_encode($this->priceArray) . "\n";
	  
		$amount = 0;
		
		if($this->type_id == 1){
			$amount += $this->priceNationalTriEye();
		}elseif($this->type_id == 2){
			$amount += $this->priceNationalSingleEye();
		}elseif($this->type_id == 3){
			$amount += $this->priceStateTriEye();
		}elseif($this->type_id == 4){
			$amount += $this->priceCountyTriEye();
		}elseif($this->type_id == 5){
			$amount += $this->priceFederalTriEye();
		}elseif($this->type_id == 6){
			$amount += $this->priceFederalState();
		}elseif($this->type_id == 7){
			$amount += $this->priceFederalDistrict();
		}elseif($this->type_id == 8){
			$amount += $this->priceEmployment();
		}elseif($this->type_id == 9){
			$amount += $this->priceEducation();
		}elseif($this->type_id == 10){
			$amount += $this->priceMvr();
		}elseif($this->type_id == 11){
			$amount += $this->priceHomeAuto();
		}elseif($this->type_id == 12){
			$amount += $this->pricePersonal();
		}elseif($this->type_id == 13){
			$amount += $this->priceHomeAuto();
		}
		
		
		return $amount;
		
    }
	
	
	public function priceNationalTriEye(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceNationalSingleEye(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceStateTriEye(){
	
		$balance = 0;
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		$state_checks = DB::table("check_state")
				        ->where("check_id", $this->check_id)
				        ->get();
								 
		$balance = count($state_checks) * floatVal($basePrice[0]);
					
		foreach($state_checks as $check){
			
			$extraCost = cache('states')
					  ->where("id", $check->state_id)
					  ->pluck('extra_cost');

			$balance += floatval($extraCost[0]);
			 
		}
		
		return $balance;
		
	}
	
	public function priceCountyTriEye(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		
		$counties = DB::table("check_county")
				 ->where("check_id", $this->check_id)
				 ->get();
		 		 	 
		$amount = count($counties) * floatVal($basePrice[0]);
		
		$passthrough = 0;
		
		foreach($counties as $county){
			
			$extraCost = cache("counties")
				         ->where("counties.id", $county->county_id)
						 ->pluck("extra_cost");
						 
			if(!is_object($extraCost)){
				$passthrough += floatval($extraCost);
			}			 
			

		}

		$amount += $passthrough;

		return $amount;
		
	}
	
	public function priceFederalTriEye(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		
		return $basePrice[0];
	}
	
	public function priceFederalState(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		
		$federalStates  = DB::table("check_state_federal")
				 		  ->where("check_id", $this->check_id)
				 		  ->get();
						  
		$balance = count($federalStates) * floatVal($basePrice[0]);
		$passthrough = 0;
		
		foreach($federalStates as $state){
			
			$amnt = DB::table("states")
			          ->where("states.id", $state->state_id)
					  ->pluck("extra_cost");
					  
			$passthrough += floatval($amnt[0]);
		}
		
		$balance += $passthrough;
		
		return $balance;
	}
	
	public function priceFederalDistrict(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		
		$count = DB::table("check_district")
				 ->where("check_id", $this->check_id)
				 ->count();
		
		$amount = $count * floatVal($basePrice[0]);

		return $amount;
	}
	
	public function priceEmployment(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceEducation(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceMvr(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceHomeAuto(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function pricePersonal(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function priceAuto(){
		
		$basePrice = $this->priceArray->where('type_id', $this->type_id)->pluck('amount');
		return $basePrice[0];
	}
	
	public function checkPrice(){
		
		
		
	}
	
	public function calculatePrice($prices){
		$this->priceArray = $prices;
		return $this->price();
	}
	
	public function prices(){
		
		return Price::where('user_id', $this->ownedBy())->get();
		//return $this->hasMany('App\Models\Price')
		       //->where('user_id', $this->ownedBy())
			   //->get();
	}
	
}