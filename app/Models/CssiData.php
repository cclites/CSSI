<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Crypt;

use Illuminate\Http\Request;
use App\Http\Requests;

class CssiData extends Model {
	
	public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	//Combines everything from infutor and usinfosearch
	public static function aggregate($infutor, $usinfo){
		
		return;
		
		$aggregate = [];
		
		$infutor = json_decode(decrypt($infutor->report));
		$usinfo = json_decode(json_decode(decrypt($usinfo->report)));
		
		/*
		$aggregate["scores"] = $infutor->Response->Detail->IDScores;
		$aggregate["properties"] = $infutor->Response->Detail->IDAttributes->PropertyAttributes;
		$aggregate["vehicles"][] =  $infutor->Response->Detail->IDAttributes->AutoAttributes->Vehicle;
		$aggregate["vehicles"][] =  $infutor->Response->Detail->IDAttributes->AutoAttributes->Vehicle2;
		$aggregate["vehicles"][] =  $infutor->Response->Detail->IDAttributes->AutoAttributes->Vehicle3;
		$aggregate["vehicles"][] =  $infutor->Response->Detail->IDAttributes->AutoAttributes->Vehicle4;
		 * 
		 */
		
		$aggregate["names"] = $usinfo->people->person->names->name;
		$aggregate["aliases"] = $usinfo->people->person->akalist->AKA;
		$aggregate["ssn"] = $usinfo->people->person->SSNs->SSNInfo;
		$aggregate["dob"] = $usinfo->people->person->DOBs;
		$aggregate["dod"] = $usinfo->people->person->DODs;
		$aggregate["addresses"] = $usinfo->people->person->addresses->address;
		$aggregate["relatives"] = $usinfo->people->person->relatives->relative;
		$aggregate["phones"] = $usinfo->people->person->otherPhones->phone;
		
		$aggregate["other"] = [];
		$other = $aggregate["other"];
		
		$other["deceased"] = $usinfo->people->person->deceased;
		$other["sexOffender"] = $usinfo->people->person->sexOffender;
		$other["bankruptcies"] = $usinfo->people->person->bankruptcies;
		$other["mostRecentBankruptcyDate"] = $usinfo->people->person->mostRecentBankruptcyDate;
		$other["bankruptcyRecords"] = $usinfo->people->person->bankruptcyRecords;
		$other["judgements"] = $usinfo->people->person->judgements;
		$other["timesreported"] = $usinfo->people->person->timesreported;
		$other["liens"] = $usinfo->people->person->liens;
		$other["mostRecentLienDate"] = $usinfo->people->person->mostRecentLienDate;
		
		return json_encode($aggregate);

	}

    //handles Auto (should be renamed to reflect this)
	public static function standardize($reports){
		
		//echo "CSSIDATA::REPORTS\n";
		//echo json_encode($reports);
		
		//return $reports;
		
		$homeAuto = [];
		$auto = [];
		$personal = [];
		
		
		//Log::info("CssiData::standardize reports");
		
		//echo "CssiData::standardize reports\n";
		
		$standardized = [];
		
		if(!empty($reports["home_auto"])){
		
		//if(!$reports["home_auto"] && $reports['auto']){
			
			//echo "Type check 11 and 13 are running\n";

			$auto = $reports["auto"];
			$autoStr = json_encode($auto);
			$autoStr = str_replace("@", "", $autoStr);
			$auto = json_decode($autoStr);
			
			
			if( isset($auto->ResponseCode) && $auto->ResponseCode > 0 && empty( $standardized["vehicles"] )){

				Log::info("No CSSI Auto records to merge; returning");
				$standardized["vehicles"][] = ['status'=>0, 'description'=>"No vehicles found"];

			}else if( empty( $standardized["vehicles"] ) ){
				
				if( isset($auto->ResponseCode) && $auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord->Vehicle)){

					$standardized["vehicles"][] = $auto->Response->Detail->NARVRecord->Vehicle;
					
				}elseif( isset($auto->ResponseCode) && $auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord)){
					
					foreach($auto->Response->Detail->NARVRecord as $v){
						$standardized["vehicles"][] = $v->Vehicle;
					}
					
				}
				
			}else{
				
			   $cars = [];
			   
			   if($auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord->Vehicle)){

					$cars[] = $auto->Response->Detail->NARVRecord->Vehicle;
					
				}elseif($auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord)){
					
					foreach($auto->Response->Detail->NARVRecord as $v){
						$cars[] = $v->Vehicle;
					}
					
				}
					
				//$auto = $auto->Response->Detail->NARVRecord->Vehicle;
				$recordFound = false;
				
				foreach($standardized["vehicles"] as &$vehicle){
	
					foreach($cars as $car){
						
						if($car->Make == $vehicle->Make && $car->Model == $vehicle->Model && $car->Year == $vehicle->Year){
							$vehicle = $car;
							$recordFound = true;
						}
						
					}
					
					if(!$recordFound){
						
						if(isset($auto->Response)){
							$standardized["vehicles"][] = $auto->Response->Detail->NARVRecord->Vehicle;
						}else{
							Log::info("No record found in Infutor Home and Auto. Attempted to append NARVRecord, but it doesn't exist");
							Log::info(json_encode($auto));
						}
						
					}
						
					$recordFound = false;
				}
			
			}
		}elseif(!empty($reports["personal"])){
			
			Log::info("CssiData::standardize - personal");
			
			//Log::info("Showing raw data from check");
			//Log::info(json_encode($reports["personal"]));
			
			if(is_string($reports['personal'])){
				$personal = simplexml_load_string($reports['personal']);
			}else{
				$personal = json_decode($reports["personal"]);
			}
			
			if(!isset($personal->people->person)){
				
				//We didn't get a record back. Now what?
				Log::info("Didn't get a record.");
				return [
					'error' => "Unable to find records."
				];
				
			}else{
				Log::info("We did get a record.");
			}
			
			$standardized["names"] = $personal->people->person->names->name;
			$standardized["aliases"] = $personal->people->person->akalist->AKA;
			$standardized["ssn"] = $personal->people->person->SSNs->SSNInfo;
			$standardized["dob"] = $personal->people->person->DOBs;
			$standardized["dod"] = $personal->people->person->DODs;
			$standardized["addresses"] = $personal->people->person->addresses;
			$standardized["relatives"] = $personal->people->person->relatives;
			$standardized["phones"] = $personal->people->person->otherPhones;
			$standardized["other"] = [];
			$other = $standardized["other"];
			$other["deceased"] = $personal->people->person->deceased;
			$other["sexOffender"] = $personal->people->person->sexOffender;
			$other["bankruptcies"] = $personal->people->person->bankruptcies;
			$other["mostRecentBankruptcyDate"] = $personal->people->person->mostRecentBankruptcyDate;
			$other["bankruptcyRecords"] = $personal->people->person->bankruptcyRecords;
			$other["judgements"] = $personal->people->person->judgements;
			$other["timesreported"] = $personal->people->person->timesreported;
			$other["liens"] = $personal->people->person->liens;
			$other["mostRecentLienDate"] = $personal->people->person->mostRecentLienDate;
	        
		}

		return $standardized;

	}

    //TODO: returns property info from Infutor
    public static function standardizeHomeAuto($reports){
    	
		//return $reports;
    	
    	if(!empty($reports["home_auto"])){
    		
			$homeAuto = $reports["home_auto"];
			
			$standardized = [];
			
			if( !empty($homeAuto->Response->Detail->IDAttributes->PropertyAttributes)){
				//$standardized["property"][] =  $homeAuto->Response->Detail->IDAttributes->PropertyAttributes;
				$standardized["property"][] =  $homeAuto->Response->Detail->IDAttributes;
			}
			
			return $standardized;

		}
    }

}