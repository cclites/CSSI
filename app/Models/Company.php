<?php

namespace App\Models;

use App\Models\User;
use App\Models\Transaction;

use Log;
use Parser;
use DB;
use Illuminate\Database\Eloquent\Model;
use Crypt;

class Company extends Model {
	
	protected $table = "users";
	protected $primaryKey = "id";
	
	public function company_checks(){

		return $this->hasMany('App\Models\Check')
				->where('company_id', $this->company_id)
			    ->where('active', true)
				->orderBy('id', 'desc');
	}
	
	public function company_this_month_checks(){

		return $this->hasMany('App\Models\Check')
            ->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfMonth(),
                \Carbon\Carbon::now()->startOfMonth()->addMonth()
            ])->where('active', true)
			->where('company_id', $this->company_id);
		
	}
	
	public function company_last_month_checks(){
		return $this->hasMany('App\Models\Check')
            ->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfMonth()->subMonth(),
                \Carbon\Carbon::now()->startOfMonth()
            ])->where('active', true)
			->where('company_id', $this->company_id);	
	}
	
	public function company_pending_checks(){
    	return $this->hasMany('App\Models\Check')
            ->whereNull('completed_at')
			->where('active', true)
			->where('company_id', $this->company_id);
    }
	
	public function members(){

		$users = User::where('company_id', $this->company_id)->get();
	
		$members = collect([]);
		
		foreach($users as $member){
			$members[] = $member;
		}
		
		return $members;
	}
	

	public static function companies($companyIds = null){
		
		if($companyIds){
			$owners = DB::table("users")->where('company_rep', true)->whereIn('company_id', $companyIds)->orderBy('company_name', 'asc')->get();
		}else{
			$owners = DB::table('users')->where('company_rep', true)->distinct('company_id')->orderBy('company_name', 'asc')->get();
		}

		$companies = collect([]);
		
		foreach($owners as $own){
			
			$company = new Company;
			$company->company_id = $own->company_id;
			$company->data = $own;
			$companies[] = $company;
		}
		
		return $companies;
	}
	
	public function id(){
		return User::where('company_id', $this->company_id)->where('company_rep', true)->pluck('id');
	}
	
	public function owner(){
		return User::where('company_id', $this->company_id)->where('company_rep', true)->first();
	}

    public function name(){
    	return \App\Models\User::where('company_id', $this->company_id)->where('company_rep', true)->pluck('name');
    }
	
	public static function balance($company_id){
		
		return DB::table("transactions")
			   ->where('parent_id', $company_id)
			   ->whereNull('invoice_id')
			   ->sum('amount');
	}

	public static function yearRange(){
	
		$max = DB::table('checks')->max('created_at');
		$min = DB::table('checks')->min('created_at');
		
		return ["max"=>$max, "min"=>$min];
	}
	
	public function transactions(){
		
		return $this->hasMany('App\Models\Transaction', 'parent_id', $this->company_id )
		       ->whereBetween('created_at', [
	                \Carbon\Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
					\Carbon\Carbon::now()->subMonth()->endOfMonth()->endOfDay()
	            ])
	            ->orderBy('id', 'desc');
	}
	
}