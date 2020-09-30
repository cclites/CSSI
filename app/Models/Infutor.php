<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Log;

class Infutor extends Model {
	
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
		
		$temp = [];
		
		if( $result->ResponseCode > 0 ){
			//return $result->ResponseMsg;
			return [];
		}else{
			
			$temp["scores"] = $result->Response->Detail->IDScores;
			$temp["properties"] = $result->Response->Detail->IDAttributes->PropertyAttributes;
			$temp["address"] = $result->Response->Detail->Identity->Address;
			$temp["ipAttributes"] = $result->Response->Detail->IDAttributes->IPAttributes;
			$temp["phones"] = $result->Response->Detail->Identity->Phones;
			$temp["emails"] = $result->Response->Detail->Identity->Emails;
			$temp["demographics"] = $result->Response->Detail->IDAttributes->DemographicAttributes->Demographics;
			$temp["vehicles"][] =  $result->Response->Detail->IDAttributes->AutoAttributes->Vehicle;
			$temp["vehicles"][] =  $result->Response->Detail->IDAttributes->AutoAttributes->Vehicle2;
			$temp["vehicles"][] =  $result->Response->Detail->IDAttributes->AutoAttributes->Vehicle3;
			$temp["vehicles"][] =  $result->Response->Detail->IDAttributes->AutoAttributes->Vehicle4;
		}
		
		return $temp;
		
	}

    public static function combineVehicleData($reports){
    	
		//Log::info(json_encode($reports));
			
		$auto = $reports["auto"];
		$autoStr = json_encode($auto);
		$autoStr = str_replace("@", "", $autoStr);
		$auto = json_decode($autoStr);
			
		if( $auto->ResponseCode > 0 && empty( $reports["home_auto"]["vehicles"] )){

			//Log::info("No CSSI Auto records to merge; returning");
			//$reports["home_auto"]["vehicles"][] = ['status'=>0, 'description'=>"No vehicles found"];

		}else if( empty( $reports["home_auto"]["vehicles"] ) ){
			
			if($auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord->Vehicle)){

				$reports["home_auto"]["vehicles"][] = $auto->Response->Detail->NARVRecord->Vehicle;
				
			}elseif($auto->ResponseCode == 0 && isset($auto->Response->Detail->NARVRecord)){
				
				foreach($auto->Response->Detail->NARVRecord as $v){
					$reports["home_auto"]["vehicles"][] = $v->Vehicle;
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
			
			foreach($reports["home_auto"]["vehicles"] as &$vehicle){

				foreach($cars as $car){
					
					if($car->Make == $vehicle->Make && $car->Model == $vehicle->Model && $car->Year == $vehicle->Year){
						$vehicle = $car;
						$recordFound = true;
					}
					
				}
				
				if(!$recordFound){
					
					if(isset($auto->Response)){
						$reports["home_auto"]["vehicles"][] = $auto->Response->Detail->NARVRecord->Vehicle;
					}else{
						Log::info("No record found in Infutor Home and Auto. Attempted to append NARVRecord, but it doesn't exist");
						Log::info(json_encode($auto));
					}
					
				}
					
				$recordFound = false;
			}
		
		    unset($reports["auto"]);
			return $reports;
    	
    	}

	}
	
}