<?php

// This file is autoloaded via composer.json
// Loads convenience functions


use \Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
//use Log;

use App\_Models\Check;
use App\_Models\Company;
use App\_Models\Invoice;
use App\_Models\Order;


use App\Models\Type;

function stripNonNumeric($value) {
  $value = preg_replace("/[^0-9]/", "", $value);
  return $value;
}


function databaseSsn($ssn) {
  // Typecast to string
  $ssn = (string)$ssn;

  // note: making sure we have something
  if(!isset($ssn{8})) { return ''; }
  // note: strip out everything but numbers
  $ssn = preg_replace("/[^0-9]/", "", $ssn);

  return $ssn;
}

function displayFormattedText($text) {
  $text = nl2br(e($text));
  $text = str_replace("\n   ", "\n&nbsp;&nbsp;&nbsp;", $text);
  $text = str_replace("\n  ", "\n&nbsp;&nbsp;", $text);
  $text = str_replace("\n ", "\n&nbsp;", $text);
  return $text;
}

function displaySsn($ssn) {
  // Typecast to string
  $ssn = (string)$ssn;

  // note: making sure we have something
  if(!isset($ssn{8})) { return ''; }
  // note: strip out everything but numbers
  return $ssn[0].$ssn[1].$ssn[2].'-'.$ssn[3].$ssn[4].'-'.$ssn[5].$ssn[6].$ssn[7].$ssn[8];
}

function displayRedactedSsn($ssn) {
  // Typecast to string
  $ssn = (string)$ssn;
  
  // note: making sure we have something
  if(!isset($ssn{8})) { return ''; }
  $ssn = preg_replace("/[^0-9]/", "", $ssn);
  // note: strip out everything but numbers
  return 'XXX-XX-'.$ssn[5].$ssn[6].$ssn[7].$ssn[8];
}

function displayMoney($amount, $decimals = 'auto') {
  if ($decimals != 'auto') {
    return '$' . number_format($amount, $decimals);
  }

  if (is_decimal($amount)) {
    return '$' . number_format($amount, 2);
  }
  
  return '$' . number_format($amount, 0);
}

function databasePhone($phone) {
  // Typecast to string
  $phone = (string)$phone;

  // note: making sure we have something
  if(!isset($phone{9})) { return ''; }
  // note: strip out everything but numbers
  $phone = preg_replace("/[^0-9]/", "", $phone);

  return $phone;
}

function displayPhone($p) {
	
  // note: making sure we have something
  //if(!isset($p{9})) { return ''; }
  
  $formatted = "(" . substr($p,-10, -7) . ") " . substr($p,-7, -4) . '-'.substr($p,-4);
  
  if(strlen($p)> 9){
	$formatted = substr($p, 0, strlen($p)-10) . " " . $formatted;
  }
  
  return $formatted; 

}


function displayDate($date, $empty = '')
{
    if (!strtotime($date)) {
        return $empty;
    }
	
    return date('M d, Y', strtotime($date));
}


function displayDateTime($datetime, $empty = '')
{

    
    if (!strtotime($datetime)) {
        return $empty;
    }
	
	$date = new DateTime($datetime->format("M d, Y g:i A"), new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('America/New_York'));
	
	return $date->format('M d, Y g:i A');

}

function displayTime($datetime, $empty = '')
{
    if (!strtotime($datetime)) {
        return $empty;
    }

    return date('g:i A', strtotime($datetime));
}


function databaseDateTime($datetime)
{
  if (!$datetime) {
    return null;
  }

    return date('Y-m-d H:i:s', strtotime($datetime));
}

function databaseDate($date)
{
    if (!$date) {
        return null;
    }

    return date('Y-m-d', strtotime($date));
}

function databaseTime($time)
{
    if (!$time) {
        return null;
    }

    return date('H:i:s', strtotime($time));
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function chunkString($content, $max_length) {
  $array = [];
  while (strlen($content)) {
      if (strlen($content) > $max_length) {
          $segment = preg_replace('/\s+?(\S+)?$/', '', substr($content, 0, $max_length));
          $content = str_after($content, $segment);
          $array[] = $segment;
      }
      else {
          $array[] = $content;
          break;
      }
  }
  return $array;
}

function decimalToHex($dec) {
    $hex = '';
    do {    
        $last = bcmod($dec, 16);
        $hex = dechex($last).$hex;
        $dec = bcdiv(bcsub($dec, $last), 16);
    } while($dec>0);
    return $hex;
}

function is_decimal( $val )
{
  return is_numeric( $val ) && floor( $val ) != $val;
}

function fullName($first, $middle, $last) {
  $full_name = $first;

  if ($full_name AND $middle) {
    $full_name .= ' '.$middle;
  }
  else {
    $full_name .= $middle;
  }

  if ($full_name AND $last) {
    $full_name .= ' '.$last;
  }
  else {
    $full_name .= $last;
  }

  return $full_name;
}

function createSeed($length = 6, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
        
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;

}

//Every time a page is loaded, the site configs are refreshed from the database.
//TODO: Add a 'dirty' flag to only run cehckConfigs if configs have been updated through the
//Configuration settings, instead of requiring a trip to the database for a value.
function checkConfigs(){
	
	//deprecated
	return;
	
	$timestamp = DB::table("configs")->where("name", 'LAST_UPDATED')->pluck('value');

	if($timestamp[0] == env('LAST_UPDATED')){
		//Log::info("Timestamps are equal");
	}else{
		//Log::info("Pulling new system configs from cssi.config");
		updateConfigs();
	}
	
}

function updateConfigs(){
	
	//deprecated
	//return;

	$configs = DB::table("configs")->get();
	$envFile = app()->environmentFilePath();
    $str = file_get_contents($envFile);
	
	foreach($configs as $config){
		$oldVal = env($config->name);
		$str = str_replace("{$config->name}={$oldVal}", "{$config->name}={$config->value}", $str);	
	}
	
	$fp = fopen($envFile, 'w');
    fwrite($fp, $str);
    fclose($fp);
	
}

function cLog($logData, $logPath, $type){

	$day = date("Y_m_d");
	
	$orderLog = new Logger($logPath);
    $orderLog->pushHandler(new StreamHandler(storage_path('logs/' . $logPath . '/' . $day . "_" . $type . '.log')), Logger::INFO);
	$orderLog->addInfo($logData);

}

function removeDuplicates($objArray){
	
	$temp = [];
	$jsonVals = [];
	
	Log::info(count($objArray));
	
	foreach ($objArray as $obj) {
		
		//$json = json_encode($obj);
		
		$hash = spl_object_hash($obj);
		
		if(!in_array($hash,$jsonVals)){
			
			$jsonVals[] = $hash;
			$temp[] = $obj;
		}
		
	}
	
	return $temp;
}

    /*
	 * Converts check from old system to new system
	 */
	function convertCheckToOrder($check){
		
		$o = new \App\_Models\Order;
		
		$o->original_id = $check->id;
		$o->company_id = $check->company_id;
		$o->user_id = $check->user_id;
		$o->first_name = $check->first_name;
		$o->middle_name = $check->middle_name;
		$o->last_name = $check->last_name;
		
		$o->created_at = $check->created_at;
		$o->updated_at = $check->updated_at;
		
		$o->completed_at = $check->completed_at;
		
		$o->reference_id = $check->provider_reference_id;
		$o->sandbox = $check->sandbox;
		
		try{
			$o->save();
			return $o;
		}catch(\Exception $e){
			return null;
		}
	
	}
	
	//creates a check from a converted order
	function createChecksFromOrder($order){
		
		Log::info("helpers::createChecksFromOrder");

		$originalId = $order->original_id;
		$companyId = $order->company_id;
		$typeIds = [3,4,6,7,10,14];
		$cnt = 0;
		
		$orderReferenceId = createSeed(6);		
		$priceObject = \App\_Models\Price::where('company_id', $order->company_id)->get();

		//These are the original check types
		$checkTypeObjs = \App\Models\Checktype::where('check_id', $order->original_id)->get();
		
		foreach($checkTypeObjs as $typeObj){
			
			
			$data = [];
			$typeId = $typeObj->type_id;
				
			if($typeId == 3){
				
				$baseAmount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				
				$checkStates = DB::table('cssi.check_state')->where('check_id', $order->original_id)->get();

				foreach($checkStates as $checkState){
					
					if(!$checkState){
						continue;
					}
					
					$state = cache('states')->where('id', $checkState->state_id)->first();
					$extraCost = $state->extra_cost;
					$amount = $baseAmount += $extraCost;
					
					createCheck($typeObj, $order, $state, $amount);
					$cnt += 1;
					
				}
					
			}elseif($typeId == 4){
				
				$baseAmount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				$checkCounties = DB::table('cssi.check_county')->where('check_id', $order->original_id)->get();
				
				if(!$checkCounties){
					continue;
				}
				
				foreach($checkCounties as $checkCounty){
					
					if(!$checkCounty){
						continue;
					}
					
					$county = cache('counties')->where('id', $checkCounty->county_id)->first();
					$extraCost = $county->extra_cost;						
					$amount = $baseAmount + $extraCost;
					
					createCheck($typeObj, $order, $county, $amount);			
					$cnt += 1;
					 
				}
				
			}elseif($typeId == 6){
				
				$baseAmount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				$checkStates = DB::table('cssi.check_state_federal')->where('check_id', $order->original_id)->get();
				
				foreach($checkStates as $checkState){
					
					if(!$checkState){
						continue;
					}
					
					$state = cache('states')->where('id', $checkState->state_id)->first();
					$extraCost = $state->extra_cost;
					$amount = $baseAmount += $extraCost;
					
					createCheck($typeObj, $order, $state, $amount);
					$cnt += 1;
				}
				
			}elseif($typeId == 7){
				
				$amount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				
				$checkDistricts = DB::table('cssi.districts')->where('check_id', $order->original_id)->get();
				
				
				
				foreach($checkDistricts as $district){
					
					if(!$district){
						continue;
					}
					
					$district = cache('districts')->where('id', $checkDistrict->district_id)->first();
	                createCheck($typeObj, $order, $district, $amount);
					$cnt += 1;
				}
				
			}elseif($typeId == 10){
				
				$baseAmount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				$checkStates = DB::table('cssi.check_state_mvr')->where('check_id', $order->original_id)->get();
				
				foreach($checkStates as $checkState){
					
					if(!$checkState){
						continue;
					}
									
					$state = cache('states')->where('id', $checkState->state_id)->first();
									
					$extraCost = $state->mvr_cost;
					$amount = $baseAmount += $extraCost;
					createCheck($typeObj, $order, $state, $amount);
					$cnt += 1;
					
				}
				
			}else{
				
				$amount = $priceObject->where('type_id', $typeId)->pluck('amount')[0];
				
				createCheck($typeObj, $order, [], $amount);
				$cnt += 1;
			}
			

		}
		
		return 0;
	}

    function createCheck($checkType, $order, $data, $amount){

		$check = new \App\_Models\Check();
		
		$check->completed_at = $checkType->created_at;
		$check->created_at = $checkType->created_at;
	    $check->updated_at = $checkType->updated_at;
		$check->amount = $amount;
		$check->type = $checkType->type_id;
		$check->provider_reference_id = $order->reference_id;
		$check->order_id = $order->id;
		$check->json_data = json_encode($data);
		$check->original_id = $checkType->check_id;
		$check->sandbox = $order->sandbox;
		
		$check->save();
	}
	
	//This does the converted checks - 
	function createCsvForChecksByCompany(){
		
		$files = glob(public_path("csvByCompany/*")); // get all file names
		
		foreach($files as $file){ // iterate files
		  if(is_file($file))
		    unlink($file); // delete file
		}
		
		$companies = Company::all();
		$types = Type::all();
		
		foreach($companies as $company){
			
			$companyName = preg_replace("/[^a-zA-Z]/", "", $company->company_name);
			$orders = Order::where('company_id', $company->company_id)->get();
			
			if(!$orders->count()){
				echo "No orders for " . $company->company_name . "\n";
				continue;			
			}
			
			$fh = fopen( public_path("csvByCompany/") . $companyName . ".csv", "a");
			fwrite($fh, "Created,First Name,Last Name,Type,Amount\n");
			
			foreach($orders as $order){

				$checks = $order->checks;

				foreach($checks as $check){
					$title = $types->where('id', $check->type)->pluck('title')->first();
					fwrite($fh, $check->created_at . "," . $order->first_name . "," . $order->last_name . "." . $title . "," .  $check->amount . "\n" );
				}

			}
			
			fclose($fh);

		}
		
	}

	//This does the converted checks - 
	function createListForYearByType(){
		
		$stateTypes = [3,6,10];
		$countyType = [4];
		$districtType = [7];
		//$cssiData = [11,12,13,14];
		
		$files = glob(public_path("csvByType/*")); // get all file names
		
		foreach($files as $file){ // iterate files
		  if(is_file($file))
		    unlink($file); // delete file
		}
		
		$types = Type::where('id', '<' , 11)->get();
		
		foreach($types as $type){
			
			$checks = Check::where('type', $type->id)->get();
			$currentTypeObj = $types->where('id', $type->id)->first();
			
			$title = $currentTypeObj->title;
			$slug = $currentTypeObj->slug;
			
			$fh = fopen( public_path("csvByType/") . $slug . ".csv", "a");
			fwrite($fh, "ID,Original ID,Created,Company,Name,Description,Amount\n");
			
			//fwrite($fh, $check->created_at . "," . $order->first_name . "," . $order->last_name . "." . $title . "," .  $check->amount . "\n" );
			
			foreach($checks as $check){
				
				$companyId = $check->order->company_id;
				$order = $check->order;
				$name = $order->first_name . " " . $order->last_name;
				$company = Company::where('company_id', $companyId)->first();
				$companyName = preg_replace("/[^a-zA-Z]/", "", $company->company_name);
				
				$companyName = str_replace(".", "", $companyName);			
				$amount = $check->amount;
				
				$description = " ";
				
				if(in_array($type->id, $stateTypes)){
					
					$data = json_decode($check->json_data);
					$description = $data->title;
					
				}
				
				if(in_array($type->id, $countyType)){
					
					$data = json_decode($check->json_data);
					$state = $data->state_code;
					$county = $data->title;
					
					$description = $county . " (" . $state . ")";
					
				}
				
				if(in_array($type->id, $districtType)){
					
					$data = json_decode($check->json_data);
					$state = $data->state_code;
					$district = $data->title;
					
					$description = $district . " (" . $state . ")"; 
					
				}
				
				$id = $check->id;
				$originalId = $check->original_id;
				
				fwrite($fh, $id ."," . $originalId . "," . $check->created_at . "," . $companyName . "," . $name . ",". $description . "," . $amount . "\n");

			}
			
			fclose($fh);
			
		}
	}
