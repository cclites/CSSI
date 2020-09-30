<?php

namespace App\Models;

use Log;
use Parser;
use Illuminate\Database\Eloquent\Model;

class Report extends Model{
	
	public $timestamps = false;
	
	protected $table = 'report';
	

	public function report(){
    	return $this->belongsTo('App\Models\Check');
    }
	
	//THis is bullshit too -
	public function check(){
    	return $this->belongsTo('App\Models\Check');
    }
	
}
