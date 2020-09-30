<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

use Illuminate\Http\Request;
use App\Http\Requests;

class DataDiver extends Model {

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	public static function NationalTriEyeApiStandardize($response){
		
		Log::info("NationalTriEyeApiStandardize");
		
		//Log::info($response);
		
		

		$xml = simplexml_load_string($response);
		
			
		unset($xml->Authentication);
		unset($xml->ProcessingRules);
		unset($xml->AddressHistoryRules);
		
		if(isset($xml->SubjectDetail)){
			$xml->SubjectDetail->SSN = '***-**-' . substr($xml->SubjectDetail->SSN, 5);
		}else{
			
			//Log::info(json_encode($xml));
			//$xml->SubjectDetail->SSN = '***-**-****';
			
		}
		
		if(!isset($xml->Response)){
			return [];
		}
		
		$xml = json_encode($xml->Response);
		$xml = str_replace("@", "", $xml);
		
		$hasOffense = false;
		$hasSexOffense = false;
		
		if(isset($xml->InstantCriminalResponse->OffenderCount) && $xml->InstantCriminalResponse->OffenderCount > 0 ){
			
			$hasOffense = true;
			
			if($xml->InstantCriminalResponse->OffenderCount == 1){
    		  $offense[] = $xml->InstantCriminalResponse->Offender;
		    }else{
		  	  $offense = $xml->InstantCriminalResponse->Offender;
		    }
			
			foreach($offense as $o){
				
				$recordArray = $o->Records;
				
				foreach($recordArray as $record=>$value){
					
					if(gettype($value) == "object"){
					   $value = array($value);
					}
					
					foreach($value as $rec){

						if( isset( $rec->SexOffenseData ) ){
							$hasSexOffense = true;
						}	
					}
				}	
			}

		}

		return [
		  'xml'=>$xml,
		  'hasOffense'=>$hasOffense,
		  'hasSexOffense'=>$hasSexOffense
		];
		
		
		//return $xml;
	}
	
	public static function NationalSingleEyeApiStandardize($response){
		
		Log::info("NationalSingleEyeApiStandardize");
		return self::NationalTriEyeApiStandardize($response);
		
	}
	
	public static function TriEyeCombineChecks($singleEye, $usinfo){
		
		$standardized = self::NationalTriEyeApiStandardize($singleEye);

		if(!isset($standardized["xml"])){
			return ['error'=>0, 'description'=>'XML does not exits in standardized'];
		}
		
		//check here for opening '<' tag to make sure we have XML before trying to continue.
		
		$singleEyeXML = json_decode($standardized["xml"]);
		
		$usinfoXML = simplexml_load_string($usinfo);

		$state = null;
		$years = null;
		
		if(isset($usinfoXML->people->person->SSNs->SSNInfo->SSNState)){
			Log::info(json_encode($usinfoXML->people->person->SSNs->SSNInfo->SSNState));
			$state = get_object_vars($usinfoXML->people->person->SSNs->SSNInfo->SSNState);
			Log::info(json_encode($state));
			
			if(isset($state[0])){
				$state = $state[0];
			}
			
			
		}
		
		if(isset($usinfoXML->people->person->SSNs->SSNInfo->SSNYears)){
			$years = get_object_vars($usinfoXML->people->person->SSNs->SSNInfo->SSNYears);
			Log::info(json_encode($usinfoXML->people->person->SSNs->SSNInfo->SSNYears));
			Log::info(json_encode($years));
			
			if(isset($years[0])){
				$years = $years[0];
			}

		}	
			
		$singleEyeXML->AddressHistoryResponse = new \stdClass;
		$singleEyeXML->AddressHistoryResponse->Summary = [
										'StateIssued'=>$state,
										'YearIssued'=>$years
									];


		if( isset($usinfoXML->people->person->addresses) ){
			Log::info("Add the addresses");
			$addresses = $usinfoXML->people->person->addresses;
			$singleEyeXML->AddressHistoryResponse->AddressHistory = new \stdClass;
			$singleEyeXML->AddressHistoryResponse->AddressHistory->Addresses = $addresses;
		}


        $standardized["xml"] = json_encode($singleEyeXML);		
		return $standardized;
	}
	
	public static function password(){
		
		return !auth()->user()->sandbox ? env("DATADIVERS_PASSWORD") : env("DATADIVERS_PASSWORD_SANDBOX");
	}
	
	public static function user(){
		return !auth()->user()->sandbox ? env("DATADIVERS_USERNAME") : env("DATADIVERS_USERNAME_SANDBOX");
	}
	
	public static function wsdl(){
		return env('DATADIVERS_URL');
	}
	
	public static function consolidateAddresses($r){

		$consolidatedAddresses = "";
		
		if(!isset($r->AddressHistoryResponse)){
			return $consolidatedAddresses;
		}
		
		$consolidatedAddresses = [];
		
		foreach($r->AddressHistoryResponse->Individual as $individual){

			foreach($individual->AddressHistory as $history){

				 foreach($history->Address as $address){

					 $consolidatedAddresses[] = $address;
				 } 
			}
		}
		
		return $consolidatedAddresses;
		
	}
	
}

?>