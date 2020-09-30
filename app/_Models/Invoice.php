<?php

namespace App\_Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

    protected $table = 'cssi._invoices';
		
	public function orders(){
		return $this->hasMany('App\_Models\Order');
	}
	
	public function newOrders(){
		return $this->hasMany('App\_Models\Order')->where("invoice_id", 0);
	}
	
	public function company(){
		return $this->belongsTo('App\_Models\Company');
	}
	
	public function total(){
		
		$orders = $this->orders();
		
		$total = 0;
		
		foreach($orders as $order){
			
			$sum = $order->checks->sum('amount');
			
			$total += $sum;
			
		}
		
		return $total;
	}

}