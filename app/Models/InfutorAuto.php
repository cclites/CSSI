<?php

namespace App\Models;

use Log;

use Illuminate\Database\Eloquent\Model;

class InfutorAuto extends Model {
	
	public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	public function state()
    {
    	return $this->belongsTo('App\Models\State');
    }
	
	public static function standardize($result){
		
		if(isset($result->Detail->__type)){
		  	unset($result->Detail->__type);
		}
		
		//Log::info(gettype($result));
		//Log::info($result->ResponseCode);
		//Log::info($result->ResponseMsg);
		
		
		if($result->ResponseCode > 0){
			//return $result->ResponseMsg;
			return [];
		}else{
			return $result;
		}
		
		
		return $result;
		
		
	}
}