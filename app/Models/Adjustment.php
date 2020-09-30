<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model {
	
	public function users(){
		return belongsToMany('App\Models\User', "User", "company_id");
	}
	
}