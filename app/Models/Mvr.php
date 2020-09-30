<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

use Illuminate\Http\Request;
use App\Http\Requests;

class Mvr extends Model {

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }

    public function state()
    {
    	return $this->belongsTo('App\Models\State');
    }
	
	public static function MvrAPIStandardize($response){
		
		Log::info("In MvrApiStandardize");
		$arrayResponse = json_decode(json_encode((array)$response), TRUE);
		

		if(!isset($arrayResponse["Formats"])){
			Log::info("FORMATS IS NOT SET");
			return null;
		}else{
			
			$data = $arrayResponse["Formats"]["FormatEntity"]["Data"];
			$data = str_replace(array("<![CDATA[","]]>", "\n", "\r", "\t"),"",$data);
			
			$dataResponse = json_decode(json_encode((array)$data), TRUE);
			$xml = simplexml_load_string($dataResponse[0]);
			
			unset($xml->DlRecord->Criteria->AccountID);
			unset($xml->DlRecord->Criteria->Subtype);
			unset($xml->DlRecord->Criteria->Purpose);
			unset($xml->DlRecord->Criteria->SubtypeFull);
			unset($xml->DlRecord->Criteria->TrackingNumber);
			unset($xml->Result->ReturnedDate);
			unset($xml->Result->ReturnedTime);
			unset($xml->Result->ReklamiErrorCode);
			unset($xml->Result->IsFromArchive);
			unset($xml->DlRecord->Criteria->CompanyName);
			
			return $xml;

		}
	
	}
	
	public static function MvrInstantAPIStandardize($response){
		
		//return self::MvrAPIStandardize($response);
		
		
		Log::info("In MvrInstantAPIStandardize");
		$arrayResponse = json_decode(json_encode((array)$response), TRUE);
		//Log::info(json_encode($arrayResponse));
		
		//echo gettype($arrayResponse) . "\n";
		//echo (json_encode($arrayResponse));

		if(!isset($arrayResponse["Formats"])){
			
			Log::info("FORMATS IS NOT SET");
			//Log::info(json_encode($arrayResponse));
			return [];
			
		}else{
			
			//echo (json_encode($arrayResponse["Formats"]["FormatEntity"]["Data"]));
			
			//Log::info(json_encode($arrayResponse["Formats"]["FormatEntity"]["Data"]));
			
			$data = $arrayResponse["Formats"]["FormatEntity"]["Data"];
			$data = str_replace(array("<![CDATA[","]]>", "\n", "\r", "\t"),"",$data);
			
			$dataResponse = json_decode(json_encode((array)$data), TRUE);
			$xml = simplexml_load_string($dataResponse[0]);
			
			
			/*
			if($xml->DlRecord->Result->Valid == "N"){
				
				$s = (array)$xml->DlRecord->Result->ErrorDescription;
				
				return ['error' => $s];
			}
			*/
			
			unset($xml->DlRecord->Criteria->AccountID);
			unset($xml->DlRecord->Criteria->Subtype);
			unset($xml->DlRecord->Criteria->Purpose);
			unset($xml->DlRecord->Criteria->SubtypeFull);
			unset($xml->DlRecord->Criteria->TrackingNumber);
			unset($xml->Result->ReturnedDate);
			unset($xml->Result->ReturnedTime);
			unset($xml->Result->ReklamiErrorCode);
			unset($xml->Result->IsFromArchive);
			unset($xml->DlRecord->Criteria->CompanyName);
			
			//echo json_encode($xml);
			
			return $xml;
		}


	}

	
	public static function password(){
		return auth()->user()->sandbox ? env("SAMBA_PASS_SANDBOX") : env("SAMBA_PASS");
		//return env("SAMBA_PASS");
	}
	
	public static function user(){
		return auth()->user()->sandbox ? env("SAMBA_USER_SANDBOX") : env("SAMBA_USER");
		//return env("SAMBA_USER");
	}
	
	public static function account(){
		return auth()->user()->sandbox ? env("SAMBA_ACCOUNT_SANDBOX") : env("SAMBA_ACCOUNT");
		//return env("SAMBA_ACCOUNT");
	}
	
	public static function wsdl(){
		return auth()->user()->sandbox ? env("SAMBA_WSDL_SANDBOX") : env("SAMBA_WSDL");
		//return env("SAMBA_WSDL");
	}
	
	public static function newPassword(){
		
		return "!" . date('j') . "C" . createSeed(6);
		
	}
	
	
}