<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\UsInfoSearch;
use App\Models\Checktype;
use App\Http\Controllers\ReportController;

use \Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use DB;
use Response;
use Crypt;

//TODO - Class and file name will change to branded name
class UsInfoCheck implements ShouldQueue{
	
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	
	public function __construct(Check $check)
    {
        $this->check = $check;
    }
	
	public function handle(){
		
		Log::info("UsInfoCheck::handle");
		
		$check = $this->check;
		
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		
		$state = $profile->state;
		
		
		$params = [
		   "username" => env('USINFO_USER'),
		   "password" => env('USINFO_PASS'),
		   "firstName" => $check->first_name,
		   "middleName" => $check->middle_name,
		   "lastName" => $check->last_name,
		   "DOB" => $profile->birthday,
		   "SSN" => $profile->ssn,
		   "state" => $state,
		   "city" => $profile->city,
		   "zip" => $profile->zip,
		   "address" => $profile->address,
		   "phone" => $profile->phone,
		   "email" => $profile->email,
		   "clientReference" => strtoupper($check->provider_reference_id),
		];
		
		Log::info("Populated params.");
		
		if($profile->birthday){
			$bday = explode("-", $profile->birthday);
		    $profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
			$params["DOB"] = $profile->birthday;
		}
		
		$payload = http_build_query($params);
		$url = env('USINFO_URL') . $payload;
		$ch = curl_init();
        
        // set up the options
        curl_setopt($ch, CURLOPT_URL, $url); // specify URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // this will have it return a string on exec call
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // time to allow it to connect
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // time to allow it to send
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);

        $results = curl_exec($ch); // perform the post
        
        if(curl_error($ch))
		{
		    echo 'Tri-Eye Personal: ' . curl_error($ch);
		}
        
        curl_close($ch); // close the session
        
        return $results;
        
        /*
		if(is_null($results)){
			return "Results are currently unavailable";
		}else{
			$report = new \App\Models\Report;
			
			$results = UsInfoSearch::standardize($results);
			
			//Log::info("*** DISPLAY THE RESULTS ***");
			//Log::info(json_encode($results));

			$report->report = encrypt(json_encode($results));
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 12;
			$report->save();

	        $checktype = $check->checktypes->where('type_id', 12)->first();
	        $checktype->is_completed();
			$checktype->save();
			
			$check->is_completed();
			$check->save();
			
			//return $results;
			
			
			if(auth()->user()->apiuser){
				
				Log::info("RETURNING PERSONAL TRI-EYE TO CONTROLLER");
				
				//Log::info(gettype($results));
				
				//Log::info($results);
				
				//$s = print_r(json_decode($results), true);
				//Log::info($s);
				
				return $results;
			}
				
		}*/
		
	}
	
	
}