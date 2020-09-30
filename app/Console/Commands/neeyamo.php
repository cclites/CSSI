<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Type;
use App\Models\Price;
use App\Models\Transaction;

use Carbon\Carbon;
use Exception;
use Log;
use SoapClient;

// Notifications
use Notification;
use \App\Notifications\BilledEmail;

//URL:  https://usverificationclientuat.neeyamo.com/ibgvwcf/neeyamoservice.svc
//Use the following web method to place the order: PlaceMultiChecks


class Neeyamo extends Command {

	protected $signature = 'neeyamo';
	protected $description = 'Send test request to Neeyamo.';

	public function __construct() {
		parent::__construct();
	}

	public function handle() {
		
		//print_r(phpinfo());
		
		$commsXML = simplexml_load_file(config_path("neeyamo/Employment-Domestic.xml"));
		$commsXML = $commsXML->asXML();
 
 
        $wsdl   = "https://usverificationclientuat.neeyamo.com/ibgvwcf/NeeyamoService.svc?singleWsdl";
		$client = new \SoapClient($wsdl, array('trace'=>1));  // The trace param will show you errors stack
		
		// web service input params
		$request_param = array(
		    "strOrderDetail" => $commsXML,
		);
		
		try
		{
		    $response = $client->PlaceMultiChecks($request_param);
			
		   //$responce_param =  $client->call("webservice_methode_name", $request_param); // Alternative way to call soap method
		} 
		catch (Exception $e) 
		{ 
		    echo "<h2>Exception Error!</h2>"; 
		    echo $e->getMessage(); 
		}
		
		var_dump($response);
       
	}

}
