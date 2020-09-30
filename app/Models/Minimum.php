<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minimum extends Model {
	
	public function users(){
		return belongsTo('App\Models\User', "User", "company_id")
						->where('authorized_rep', true);
	}
	
}