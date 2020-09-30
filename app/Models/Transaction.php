<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {


    // Relationships
    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function invoice(){
    	return $this->belongsTo('App\Models\Invoice');
    }
	
	public function check(){
		return $this->belongsTo('App\Models\Check');
	}
	
	public function type(){
		return \App\Models\Type::find($this->check_type);
	}

}