<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model {

	public $timestamps = false;
	
	public function getFormattedPriceAttribute()
	{
	    return number_format($this->attributes['default_price'], 2);
	}
	
	

	
}