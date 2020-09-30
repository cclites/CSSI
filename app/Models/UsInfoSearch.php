<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Log;

class UsInfoSearch extends Model {
	
	public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	public function state()
    {
    	return $this->belongsTo('App\Models\State');
    }
	
	public static function standardize($results){
		
		$xml = simplexml_load_string($results);
		unset($xml->searchInput);
		unset($xml->searchTime);
		unset($xml->rows);

        return $xml;

	}
	
}