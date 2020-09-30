<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class State extends Model {

	public $timestamps = false;

	public function getTitleWithExtraCostAttribute()
	{
		if ($this->extra_cost > 0) {
			return $this->title . ' + '.displayMoney($this->extra_cost);
		}

		return $this->title;
	}
	
	public static function getStateById($id){
		return DB::table("states")->where('id', $id)->first();
	}
	
	public static function getStateByCode($code){
		return DB::table("states")->where('code', $code)->first();
	}
	
	public function getFormattedPriceAttribute()
	{
	    return number_format($this->attributes['mvr_cost'], 2);
	}
}