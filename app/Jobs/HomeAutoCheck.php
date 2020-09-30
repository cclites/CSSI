<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Infutor;
use App\Models\InfutorAuto;
use App\Models\CssiData;
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
class HomeAutoCheck implements ShouldQueue{
	
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;
	
	public function __construct(Check $check)
    {
        $this->check = $check;
    }
	
	public function handle(){
		
		Log::info("HomeAutoCheck::handle");
		
		$check = $this->check;
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		$bday = explode("-", $profile->birthday);
		
		$params = [
		   "Login" => env('INFUTOR_USER'),
		   "Password" => env('INFUTOR_PASS'),
		   "FName" => $check->first_name,
		   "LName" => $check->last_name,
		   "state" => $profile->state,
		   "city" => $profile->city,
		   "zip" => $profile->zip,
		   "address1" => $profile->address,
		   "phone" => $profile->phone,
		   "phone2" => $profile->phone2,
		   "email" => $profile->email,
		   "ip" => $profile->ip,
		];
		
		$payload = http_build_query($params);
		$url = env('INFUTOR_URL') . $payload;
		$ch = curl_init();
        
        // set up the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);

        $results = curl_exec($ch); // perform the post

        if(curl_error($ch))
		{
		    Log::info('error:' . curl_error($ch));
		}
        
        curl_close($ch); // close the session
        
        if(is_null($results)){
			//return "Results are currently unavailable";
			return [];
		}else{
			$report = new \App\Models\Report;

			//possibly need to clean the results first
			$xml = new \SimpleXMLElement($results);
			$results = Infutor::standardize($xml);
			//$results = $xml;
			$report->report = encrypt(json_encode($results));
			$report->check_id = $check->id;
			$report->tracking = strtoupper($check->provider_reference_id);
			$report->check_type = 11;
			$report->save();
			
	        $checktype = $check->checktypes->where('type_id', 11)->first();
	        $checktype->is_completed();
			$checktype->save();
			
			$check->is_completed();
			$check->save();
			
			if(auth()->user()->apiuser){
				Log::info("RETURNING HOME & AUTO TRI-EYE TO CONTROLLER");
				return $results;
			}
				
		}
		
	}
	
	
}