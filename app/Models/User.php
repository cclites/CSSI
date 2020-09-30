<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use App\Models\Check;
use App\Models\Company;


use DB;
use Auth;
use Log;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        
    ];

    protected $fillable = [
        'id'
    ];

    // Attributes
    public function getFullNameAttribute()
    {
        $name = ucfirst($this->first_name);
        
        if ($name AND $this->last_name) {
            $name .= ' ';
        }

        $name .= ucfirst($this->last_name);

        return $name;
    }
	
	
	//This has to be changed.
	public function company(){
		
		$company = new Company;
		
		if( Auth::user() ){
			$companyId = Auth::user()->company_id;
		}else{
			Log::info("No Auth user");
		}
		
		if ( auth()->user() ){
			$companyId = auth()->user()->company_id;
		}else{
			Log::info("No auth()->user");
		}
		
		if($this->company_id){
			$companyId = $this->company_id;
		}

        //Yeah, I realize this is set two different ways
		$company->id = $companyId;
		$company->company_id = $companyId;

		return $company;
	}

    public function getBalanceAttribute()
    {
        return $this->transactions->sum('amount');
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address;
        
        if ($this->secondary_address) {
            $address .= ', '.$this->secondary_address;
        }
        if ($this->city) {
            $address .= ', '.$this->city;
        }
        if ($this->state) {
            $address .= ', '.$this->state;
        }
        if ($this->zip) {
            $address .= ' '.$this->zip;
        }

        return $address;
    }

    public function getDisplayPhoneAttribute()
    {
        return displayPhone($this->phone);
    }

    // Relationships
    public function checks()
    {
		$admin = false;
		
		try{
			$roles = $this->getRoleNames();
			$roles = explode(",", $roles);
			
			if(in_array('admin', $roles)){
				$admin = true;
			}
		}catch(Exception $e){
			Log::info($e);
		}
		
		//show all checks to admin
		if($admin){
			return $this->hasMany('App\Models\Check')
	               ->orderBy('id', 'desc');
		}else{
			
			//show active checks for user
			return $this->hasMany('App\Models\Check')
				   ->where('active', true)
	               ->orderBy('id', 'desc');
					
		}
	
    }

    public function this_month_checks()
    {
       
	    return $this->hasMany('App\Models\Check')
            ->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfMonth(),
                \Carbon\Carbon::now()->startOfMonth()->addMonth()
            ])->where('active', true);
    }
	
    public function last_month_checks()
    {
        return $this->hasMany('App\Models\Check')
            ->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfMonth()->subMonth(),
                \Carbon\Carbon::now()->startOfMonth()
            ])->where('active', true);
    }


    public function pending_checks()
    {
        return $this->hasMany('App\Models\Check')
            ->whereNull('completed_at')
			->where('active', true);
    }

    public function transactions()
    {	
		return $this->hasMany('App\Models\Transaction')
		       ->whereBetween('created_at', [
	                \Carbon\Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
					\Carbon\Carbon::now()->subMonth()->endOfMonth()->endOfDay()
	            ])->orderBy('id', 'desc');	
			
    }
	
	public function currentTransactions(){
		
		return $this->hasMany('App\Models\Transaction')
		       ->whereBetween('created_at', [
	                \Carbon\Carbon::now()->startOfMonth()->startOfDay(),
					\Carbon\Carbon::now()->endOfMonth()->endOfDay()
	            ])->orderBy('id', 'desc');
		
	}
	

    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice')
            ->orderBy('id', 'desc');
    }

    public function prices()
    {
        return $this->hasMany('App\Models\Price');
    }
	
	
	//this is a stupid way to do this - should just get all prices by
	//user from the Prices table. It is also screwed up because every
	//tiem a check is added, this needs to be updated manually
	public function priceArray(){
		
		$p = $this->prices;
		$pArray = [];
		
		
		foreach($p as $price){
			$pArray[$price->type_id . ""] = $price->amount;
		}
		
		return $pArray;
	}

    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }
	
	public function hasApiRole(){
		
		return $this->hasRole('apiauth') ? true : false;
	}
	
	public function adjustment(){
		
		//return $this->hasOne("\App\Models\Adjustment", "company_id", $this->company_id);
		
		return \App\Models\Adjustment::where("company_id", $this->company_id)->sum('amount');
	}
	
	public function minimum(){
		
		return $this->hasOne("\App\Models\Minimum", "company_id", $this->company_id);
		//return \App\Models\Minimum::where("company_id", $this->company_id)->first();
	}
	
}
