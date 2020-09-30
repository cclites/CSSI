<?php

namespace App\_Models;

use Log;
use Parser;
use DB;
use Illuminate\Database\Eloquent\Model;
use Crypt;

class Order extends Model {
	
	protected $table = 'cssi._orders';
	
	public function adjustment(){
		return \App\_Models\Adjustment::where("company_id", $this->company_id)->sum('amount');
	}
	
	public function minimum(){
		return $this->hasOne("\App\_Models\Minimum", "company_id", $this->company_id);
	}
	
	public function checks(){
		return $this->hasMany("\App\_Models\Check")->where('order_id', $this->id)->orderBy('id', 'desc');
	}
	
	public function completedChecks($startDate, $endDate){
		return \App\_Models\Check::where('completed_at', ">=", $startDate)->where('completed_at', "<=", $endDate)->get();	
	}
	
	public function profile(){
		return $this->hasOne("\App\_Models\Profile");
	}
	
	public function company(){
		return $this->belongsTo('\App\_Models\Company', "company_id");
	}
	
	public function amount(){
		return $this->checks->sum('amount');
	}

}