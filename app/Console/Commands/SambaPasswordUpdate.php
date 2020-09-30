<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

// Models
use App\Models\Mvr;
use App\Models\Stat;


use DB;
use Log;

use Carbon\Carbon;
use Exception;

class SambaPasswordUpdate extends Command{
	
	protected $signature = 'sambaPasswordUpdate {testing}';
	protected $description = "Script to update Samba passwords";
	protected $stat;
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
		$this->stat = Stat::where('name', 'MvrCheck')->first();
		$stat = $this->stat;
		
		$type = \App\Models\Type::where('id', 10)->first();
		
		//$testing = $this->argument('testing');
		$testing = true;
		echo "Testing mode is $testing\n";
		
		if($type->enabled){
			Log::info("Check is enabled");
			$stat->val = "Running Update.";
			$stat->save();
		}else{
			Log::info("Check is not enabled");
			//return;
		}
		
		echo "Generate new pssword\n";
		
		$newPassword = Mvr::newPassword();
		
		if($testing){
			$pass = env("SAMBA_PASS_SANDBOX");
			$user = env("SAMBA_USER_SANDBOX");
			$account = env("SAMBA_ACCOUNT_SANDBOX");
			
			Log::info("New MVR SANDBOX password is " . $newPassword);
			echo "Using Test credentials\n";
			//echo 
		}else{
			//$pass = env("SAMBA_PASS");
			//$user = env("SAMBA_USER");
			//$account = env("SAMBA_ACCOUNT");
			
			Log::info("New MVR password is " . $newPassword);
			echo "Trying to use live credentials\n";
			return;
		}

		echo "New password is " . $newPassword . "\n";

		$payload = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'.
	    				'<s:Body>'.
						   '<ChangePassword xmlns="http://adrconnect.mvrs.com/adrconnect/2013/04/">';
						   
		$payload .= '<inAccountID>' . $account . '</inAccountID>' .
			          '<InUserID>' . $user . '</InUserID>' .
			          '<inCurrentPassword>' . $pass . '</inCurrentPassword>' .
			          '<inNewPassword>' . $newPassword . '</inNewPassword>';
						       
							   
		$payload .=		   '</ChangePassword>'.
				  		'</s:Body>'.
    			   '</s:Envelope>';
			   
		Log::info($payload);
		
		echo $payload . "\n";
		
		//Temporarily disable the MVRs
		echo "Disabling MVR check type\n";
		sleep(8);
		DB::table("types")->where('id', 10)->update(["enabled"=> false]);
		
		//send the request
		$response = ReportController::sendReportOrderSoap($payload, "ChangePassword", true);
		
		try{
			
			$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
			
			$xml = simplexml_load_string($response);
			$errorId = $xml->sBody->ChangePasswordResponse->ChangePasswordResult->CallValidation->ErrorId;
			$errorDescription = $xml->sBody->ChangePasswordResponse->ChangePasswordResult->CallValidation->ErrorDescription;
			$message = $xml->sBody->ChangePasswordResponse->ChangePasswordResult->CallValidation->Message;

			if( $errorId == 0){
				
				echo "Value of pass is $pass\n";
	            
				DB::table('configs')
			         ->where('value', $pass)
					 ->update(['value'=>$newPassword]);
					 
				updateConfigs();
				
				sleep(5);

				//re-enable the MVRs
				echo "Re-enabling the MVRs\n";
			    DB::table("types")->where('id', 10)->update(["enabled"=> true]);
				
				$stat->val = "45";
			    $stat->save();
			}else{
				$stat->val=0;
				$stat->description = $errorDescription;
			    $stat->save();
			}

		}catch(\Exception $e){
		    Log::info("Password reset error");
			Log::info($e->getMessage());
			
			$stat->val = 0;
			$stat->save();
		}
			
		return;
		
		
	}
	

}