<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neeyamo extends Model {

	public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	public static function password(){
		return env("NEEYAMO_PASS");
	}
	
	public static function user(){
		return env("NEEYAMO_USER");
	}
	
	public static function clientId(){
		return env("NEEYAMO_CLIENT_ID");
	}
	
	public static function clientCode(){
		return env("NEEYAMO_CLIENT_CODE");
	}
	
	public static function url(){
		return env("NEEYAMO_URL");
    }
	
	public static function parseInsufficiencies($xmlString){
		
		return $xmlString;
		
		//$xml = simplexml_load_string($xmlString);
		
		//$message = "Incorrectly parsed insufficiency";
			
		//if(isset($xml->InsufficiencyInformation->Insufficiency)){
			//$message = $xml->InsufficiencyInformation->Insufficiency;
		//}
		//return $message;
		
		//email the details to the client. Unsure what those may be though.
		
		//parse the report to get insufficiencies
	}
	
	public static function parseStatus($xmlString){
		
		$xml = simplexml_load_string($xmlString);
		
		/*
		if($typeId == 8){
			$report = [
			    'status' => $xml->HRDetails->OrderStatus
			];
		}elseif($typeId == 9){
			$report = [
			    'status' => $xml->VerifiedDetail->OrderStatus
			];
		}
		*/
		return json_encode($xml);
		
	}
	
	public static function parseResult($xml, $typeId){
		
		//$xml = simplexml_load_string($xmlString);
		return $xml;

		/*
		if($typeId == 8){
			
			$report = [
			  'reportingManagerName' => $xml->ReportingManagerDetails->Reportingmanagername,
			  'reportingManagerPhone' => $xml->ReportingManagerDetails->reportingmanagercontactno,
			  'reportingManagerEmail' => $xml->ReportingManagerDetails->reportingmanagerEmailid,
			  'HRName' => $xml->HRDetails->HRName,
			  'HRPhone' => $xml->HRDetails->HRContactNo,
			  'HREmail' => $xml->HRDetails->HREmailId,
			  'additionalInformation' => $xml->HRDetails->AdditionalInformation,
			  'verifierDetail' => $xml->HRDetails->VerifierDetail,
			  'verifierComments' => $xml->HRDetails->VerifierComments,
			  'status' => $xml->HRDetails->OrderStatus
			];
			
		}elseif($typeId == 9){
			
			$report = [
			  'additionalInformation' => $xml->VerifiedDetail->AdditionalInformation,
			  'verifierDetail' => $xml->VerifiedDetail->VerifierDetail,
			  'verifierComments' => $xml->VerifiedDetail->VerifierComments,
			  'status' => $xml->VerifiedDetail->OrderStatus
			];
			
		}

		return json_encode($report);
		 * 
		 */
		
	}
	
	
		
}