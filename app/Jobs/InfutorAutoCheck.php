<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\InfutorAuto;
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
class InfutorAutoCheck implements ShouldQueue{
	
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	
	public function __construct(Check $check)
    {
        $this->check = $check;
    }
	
	public function handle(){
		
		Log::info("Running Infutor Auto Check");
		
		$check = $this->check;

		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		$bday = explode("-", $profile->birthday);
		//$profile->birthday = Carbon::createFromDate($bday[0], $bday[1], $bday[2]);
		
	
		$params = [
		   "login" => env('INFUTOR_USER'),
		   "password" => env('INFUTOR_PASS'),
		   "fname" => $check->first_name,
		   "lname" => $check->last_name,
		   "state" => $profile->state,
		   "city" => $profile->city,
		   "zip" => $profile->zip,
		   "address1" => $profile->address,
		   "multiple" => "true"
		];
		
		$payload = http_build_query($params);
		$url = env('INFUTOR_AUTO_URL') . $payload;
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
		    echo 'error:' . curl_error($ch);
		}
        
        curl_close($ch); // close the session
        
        Log::info(json_encode($results));
        
        if(is_null($results)){
			return "Results are currently unavailable";
		}else{
			$report = new \App\Models\Report;

			$xml = new \SimpleXMLElement($results);
			$standardized = InfutorAuto::standardize($xml);
			$report->report = encrypt(json_encode($standardized));
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 13;
			$report->save();

	        $checktype = $check->checktypes->where('type_id', 13)->first();
	        $checktype->is_completed();
			$checktype->save();
			
			if(auth()->user()->apiuser){
				Log::info("RETURNING INFUTOR AUTO CHECK TO CONTROLLER");
				return $standardized;
			}
				
		}
		
	}
	
	
}