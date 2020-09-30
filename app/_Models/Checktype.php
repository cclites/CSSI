<?php
namespace App\_Models;

use Illuminate\Database\Eloquent\Model;

/* _Models */
use App\_Models\Order;
use App\_Models\Price;
use App\_Models\Type;

/* Models */
use App\Models\User;


/* Facades */
use DB;
use Log;

class Checktype extends Model {

	public $timestamps = false;

	protected $table = 'check_type';
	
	protected $ownerId = null;
	protected $priceArray = null;


    public function order(){
    	return $this->belongsTo('App\_Models\Order');
    }


    public function type()
    {
    	return $this->belongsTo('App\Models\Type');
    }

	public function is_completed(){
		//Log::info("Setting Checktype completed");
		$this->completed_at = \Carbon\Carbon::now();
        //$this->save();
        return true;
	}
	
	public function ownedBy(){
		
		if(isset($this->ownerId)){
			//Log::info("Owned by is set");
			return $this->ownerId;
		}
		
		//$order = Order::where('check')
		
		/*
		 * $this
		 * 
		 * {"id":9069,"check_id":136167,"type_id":1,"completed_at":"2018-12-18 17:54:05","created_at":"2018-07-31 15:28:07","enabled":0}
		 * 
		 */
		
		$order = Order::where('original_id', $this->check_id)->first();
		
		
		/*
		{"id":1,"company_id":"oBUIb7","original_id":136167,"invoice_id":0,"user_id":3324,"first_name":"iva","middle_name":null,"last_name":"monzon","has_offense":0,"has_sex_offense":0,"stripe_charge":null,"viewed":0,"adjustment_applied":"0.00","completed_at":"2018-12-18 17:54:05","created_at":"2018-07-31 15:28:07","updated_at":"2018-12-18 17:54:05","reference_id":"bjTHbj"}
		 */
		 
		$companyId = $order->company_id;
		
		//echo "Company id is " . $companyId . "\n";
		
		//die();
		
		$owner = User::where('company_id', $companyId)->where('company_rep', true)->first();
		
		//what happens if there is no owner?
		
		if(!$owner){
			echo gettype($owner) . "\n";
			echo json_encode($order) . "\n";
			echo $companyId . "\n\n";
			return null;
		}
		
		$prices = \App\Models\Price::where('user_id', $owner->id)->get();
		
		if(!$prices){
			echo "No prices for this user: " . $owner->id;
			die();
		}else{
			$this->ownerId = $owner->id;
			return $owner->id;
		}
		
		/*
		 {"id":1993,"company_id":"ixEqMJ","original_id":160883,"invoice_id":0,"user_id":3550,"first_name":"PEDRO","middle_name":null,"last_name":"CABRERA","has_offense":0,"has_sex_offense":0,"stripe_charge":null,"viewed":0,"adjustment_applied":"0.00","completed_at":"2018-12-17 12:42:24","created_at":"2018-12-17 12:42:22","updated_at":"2018-12-17 13:00:03","reference_id":"hRxH8C"}
		 * 
		 */
		

		//somebody doesnt have prices set -
		/*
		if(!$owner->id){
			echo "There is no owner?\n";
			echo json_encode($owner) . "\n\n";
			echo json_encode($order) . "\n\n";
		}
		*/
		/*
		echo json_encode($owner) . "\n";
		die();
		
		if(!$owner){
			
			echo "Company ID $companyId\n";
			echo json_encode($order) . "\n";
			
			
		}else{
			//echo $owner->id . "\n";
			$this->ownerId = $owner->id;
		    return $this->ownerId;
		}
	    */
	    
		//echo json_encode($order) . "\n";
		//die();
		
		/*
		$companyId = $order->company_id;
		 */
		
		
        /*
		 {"id":3327,"whitelabel_id":null,"key":"3327-13453","first_name":"Laura","last_name":"Pfifer","email":"lpfifer@nescoresource.com","password_reset":"i0ZsshgZ2b2fAYjmDUVi6m8nDQaAps0rjAJJp2fZWG8OEpBLQjL38spI6kSpcIMra91l3UA6rKOWfKIgHTsW6OE24jf8lIkmWpJH","company":null,"address":"4472 Park Blvd","secondary_address":null,"city":"Pinellas Park","state":"FL","zip":"33781","country":"USA","phone":"17275444500","website":null,"fail_criteria":null,"ip":"172.31.39.251","is_approved":1,"is_setup_contact":1,"is_suspended":0,"is_sidebar":1,"admin_notes":null,"stripe_customer_id":null,"card_brand":null,"card_last_four":null,"card_expiration":null,"created_at":"2018-06-20 19:25:25","updated_at":"2018-08-13 14:07:55","company_id":"oBUIb7","sandbox":0,"company_rep":1,"company_name":"Nesco Resource - Pinellas Park","invoice":"mgroomes@nescoresource.com","cell_phone":null,"extension":null,"is_app":0,"device":null}
		*/
		
		
		 
		   
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
						 
			//echo $extraCost . "\n";
			//echo gettype($extraCost) . "\n";
			
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
	
	public function prices(){
		
		return Price::where('user_id', $this->ownedBy())->get();

	}
	
	public function getCheckPrice($companyId){
		
		
		
		$prices = \App\_Models\Price::where('companies_id', $company->company_id)->get();
		return $prices;
		
		//$this->prices = \App\_Models\Price::where("companies_id", $companyId)->get();
		//return $this->price();
	}

}