<?php

namespace App\Http\Controllers\Library\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

//models
use App\Models\Check;

//facades
use Log;
use Auth;
use DB;

class ChecksLibrary{
	
	//Grab just the checks the limited admin can see.
	public function checksForLimitedAdmin($request){
		
		Log::info("Getting checks for limited admin");

		$companies = DB::table("viewable_companies")
					->where('user_id', Auth::user()->id )
					->pluck('company_id')->toArray();
					
		return Check::whereIn('checks.company_id', $companies);
	}
	
	public function convertStringArrayToIntArray($array){
		
		$newTypes = [];

		for($i=0; $i < count($array); $i += 1){
			$newTypes[] = (int)$array[$i];
		}
		
		return $newTypes;
	}
	
	public function validateCvsUploadFileType(){
		
		$errors = [];
		
		$filename = $_FILES['import']['name'];
		$fileSplit = explode(".", $filename);
		
		if(!isset($fileSplit[1]) || $fileSplit[1] !== 'csv'){
			Log::info("Incorrect File Type");
			$errors[] = "Incorrect file type";
		}
		
		$uploaded_file_mime = $_FILES['import']['type'];

		if($uploaded_file_mime != 'application/vnd.ms-excel'){
			Log::info("Incorrect mime type");
			$errors[] = "Incorrect mime type";
		}
		
		return $errors;
		
	}
	
	public function validateBulkTriEye(){
		
		$errors = [];
		$file = file_get_contents($_FILES['import']['tmp_name']);
		$file = preg_split("/\\r\\n|\\r|\\n/", $file);
		$cntr = 0;
		
		foreach($file as $f){
			
			//skip the title row of the import file
			if($cntr == 0){
				Log::info("Skip the first row of an import file");
				$cntr += 1;
				continue;
			}
			
			//skip the empty row
			if(empty($f) ){
				continue;
			}

			$tuples = explode(",", $f);
			$tuples = array_map("trim", $tuples);

			if( empty($tuples[0]) ){
				Log::info("Row is empty - skip: " . $f);
				continue;
			}
			
			if( empty($tuples[0]) || empty($tuples[2]) || empty($tuples[3]) || empty($tuples[4])){
				$errors[] = "Input is invalid: " . $f;
				continue;
			}
			
			if( !empty($tuples[3]) && !preg_match('#^(\d{3})-(\d{2})-(\d{4})$#', $tuples[3]) ){
				$errors[] = "SSN is improperly formatted: " . $f;
			}
			
			list($mm,$dd,$yyyy) = explode('/',$tuples[4]);
			
			if (!checkdate($mm,$dd,$yyyy)) {
        		$errors[] = "Birthdate is invalid: " . $f;
			}
		}

		return $errors;
		
	}

	public function idIsDistinct($id){
		
		$distinct = DB::table("check_distinct")->where('token', $id)->first();
		return is_null($distinct);
	}
	
	public function checkIsDistinct($request){
			
		$firstName = $request->first_name;
		$lastName = $request->last_name;
		$userId = Auth::user()->id;
		
		$check = Check::where('user_id', $userId)
			               ->where('first_name', $firstName)
						   ->where('last_name', $lastName)
						   ->where('created_at', '>=', 'CURRENT_TIMESTAMP - INTERVAL 2 MINUTE')
						   ->first();
						   
		return is_null($check);
		
	}
	
	public function rawCheckCounts(Request $request){
		
		$types = DB::table('types')->get();
		$dailies = DB::table('_dailies')->where('day', '>=', 20190401)->get();
		
		$totals = [];
		
		foreach($dailies as $daily){
			
			$dailyChecks = json_decode($daily->checks);
			$checkCount = count($dailyChecks);
			$typeName = $types->where('id', $daily->type)->pluck("title")->first();
			
			if(isset($totals[$daily->day][$typeName]["count"])){
				
				$totals[$daily->day][$typeName]["count"] += $checkCount;
				
			}else{
				
				$totals[$daily->day][$typeName]["count"] = $checkCount;
				
			}
			
		}
		
		return $totals;
		
	}
	
}
