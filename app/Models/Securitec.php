<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

use Illuminate\Http\Request;
use App\Http\Requests;

class Securitec extends Model {
	
	// Relationships
    public function check(){
    	return $this->belongsTo('App\Models\Check');
    }
	
	public static function SecuritecApiStandardize($response){
		$response = preg_replace('/[a-zA-Z]+:([a-zA-Z]+[=>])/', '$1', $response);
		$xml = new \SimpleXMLElement($response);
		return $xml->BackgroundReportPackage->Screenings;
	}
	
	public static function password(){
		return !auth()->user()->sandbox ? env("SECURITEC_PASS"): env("SECURITEC_PASS_SANDBOX");
	}
	
	public static function user(){
		return !auth()->user()->sandbox ? env("SECURITEC_USER"): env("SECURITEC_USER_SANDBOX");
	}
	

}

?>