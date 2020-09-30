<?php

namespace App\Jobs;

use App\Http\Controllers\Api\V1\CheckController;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Models\Profile;
use App\Models\Check;

use App\Jobs\NewNationalTriEyeCheck;

use App\Notifications\BulkUploadCompleteEmail;
use App\Recipients\InvoiceRecipient;

use Log;
use DB;
use Response;
use Crypt;
use Auth;

class BulkNationalTriEyeCheck implements ShouldQueue{
	
	protected $file;
	protected $client;
	protected $type;
	protected $fileName;
	
	public function __construct($file, $client, $type, $fileName)
    {
        $this->file = $file;
		$this->client = $client;
		$this->type = $type;
		$this->fileName = $fileName;
    }
	
	public function handle(){
		
		Log::info("Run Jobs/BulkNationalTriEyeCheck");
		
		//$file = $this->file;
		
		//echo $file[0] . "\n";
		
		//print_r($file);
		$file = file_get_contents(storage_path("app/batch/" . $this->fileName));
		
		$possibleLineEnds = array("\r\n", "\n\r", "\r", "<br>", "<br/>", "&lt;br/&gt;");
		$fileData = explode("\n", $file);
		$file = $fileData;
		
		print_r($file);
		
		$client = $this->client;
		$type = $this->type;
		$fileName = $this->fileName;
		$cntr = 0;
		
		//$path = storage_path("app/batch/" . $fileName);
			
		//$file = stripcslashes( file_get_contents($path) );
		
		
		
		//print_r($client);
		//echo json_encode($file) . "\n";
		//echo gettype($client) . "\n";
		
		//return 0;
		
		foreach($file as $f){
			

			if($cntr == 0){
				$cntr += 1;
				Log::info("Skipped the first row because it is the label row. - skip");
				continue;
			}
			
			
			
			if(empty($f)){
				Log::info("Skipped row because row is empty - skip");
				continue;
			}

			$tuples = explode(",", $f);
			$tuples = array_map("trim", $tuples);
			
			
			if( empty($tuples[0]) || empty($tuples[2]) || empty($tuples[3]) || empty($tuples[4])){
				//$errors[] = "Input is invalid";
				Log::info("Tuples[0], Tuples[2], Tuples[3] or Tuples[4] is empty - skip");
				Log::info(json_encode($tuples));
				continue;
			}
			 
			$myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['first_name' => $tuples[0]]);
			$myRequest->request->add(['middle_name' => $tuples[1]]);
			$myRequest->request->add(['last_name' => $tuples[2]]);
			$myRequest->request->add(['ssn' => $tuples[3]]);
			$myRequest->request->add(['birthday' => $tuples[4]]);
			$myRequest->request->add(['check_types' => [$type]]);
			
			echo "Built the myRequest object\n";

			$profile = new Profile;
		    $params = $profile->createProfileData($myRequest);
		    
		    echo "Created the profile and params\n";
			
			echo($client["id"]) . "\n";
			echo($client["sandbox"]) . "\n";
			echo($client["company_id"]) . "\n";
			
			$check = new Check;
            $check->user_id = $client["id"];
            $check->first_name = $tuples[0];
            $check->middle_name = $tuples[1];
            $check->last_name = $tuples[2];
		    $check->provider_reference_id = createSeed(12); //auto-generated
		    $check->sandbox = $client["sandbox"];
		    $check->company_id = $client["company_id"];
			$check->save();
			
			$check->types()->attach(1);
			
			$profile->profile = encrypt(json_encode($params));
		    $profile->check_id = $check->id;
		    $profile->save();
			
			echo "Saved the profile\n";
			
			dispatch(new NewNationalTriEyeCheck($check));
			
		}

		$recipient = new InvoiceRecipient($client->email);
        $recipient->notify(new BulkUploadCompleteEmail($type, $client->getFullNameAttribute()));
		
		unlink( storage_path("app/batch/" . $fileName) );
        
        return 0;
 
	}
}
