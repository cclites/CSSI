<?php

namespace App\_Models;

use Illuminate\Database\Eloquent\Model;

/* FACADES */
use Log;
use Carbon\Carbon;


class Company extends Model {
	
	protected $table = "cssi._companies";

	public function users(){
		return $this->hasMany('App\_Models\User');
	}
	
	public function rep(){
		return $this->hasOne('\App\_Models\User')->where('id', $this->company_rep);
	}
	
	public function prices(){
		return $this->hasMany('App\_Models\Price');
	}
	
	public function orders(){
		return $this->hasMany('App\_Models\Order')->orderBy('id', 'desc');
	}
	
	public function invoices(){
		return $this->hasMany('App\_Models\Invoice')->orderBy('id', 'desc');
	}
	
	public function hasApiRole(){
		return $this->hasRole('apiauth') ? true : false;
	}
	
	public function ordersForInvoice(){
		
		$start = Carbon::today()->startOfMonth()->subMonth()->startOfDay();
	    $start->setTimezone('UTC');
	    
	    $end = Carbon::today()->endOfDay()->subMonth()->endOfMonth();
	    $end->setTimezone('UTC');
		
		return \App\_Models\Order::where('completed_at', ">=", $start)
					->where('completed_at', "<=", $end)
					->where('company_id', $this->company_id)
					->orderBy('id', 'desc')
					->get();
	}
	
}