<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Type;
use App\Models\Price;
use App\Models\Report;
use App\Models\Transaction;

use Carbon\Carbon;
use Exception;
use Log;
use Crypt;
use DB;

class checkOffense extends Command{
	
	protected $signature = 'checkOffense';
	
	public function __construct()
    {
        parent::__construct();
    }
	
	public function handle(){
		
	  $hasOffenses = [];
	  $hasSexOffender = [];
	
	  $user = User::find(3195);
	
	  foreach($user->checks as $check){
	  	
		//echo json_encode($check) . "\n";
		
		$reports = Report::where("check_id", $check->id)->whereIn('check_type', [1,2])->get();
		  
		foreach($reports as $report){
			//echo json_encode($report) . "\n";
			
			$data = json_decode(decrypt($report->report));
				
			if( gettype($data) == 'string'){
			  	$data = json_decode($data);
			}
			
			if(isset($data->InstantCriminalResponse->OffenderCount) && $data->InstantCriminalResponse->OffenderCount > 0 ){
					
				$hasOffenses[] = ["link"=> "https://api.eyeforsecurity.com/checks/" . $check->id, "name"=> $check->first_name . " " . $check->last_name];
				
				
				if($data->InstantCriminalResponse->OffenderCount == 1){
	    		  	$offense[] = $data->InstantCriminalResponse->Offender;
    		    }else{
    		  	  $offense = $data->InstantCriminalResponse->Offender;
    		    }
				
				foreach($offense as $o){
					
					$recordArray = $o->Records;
					
					
					foreach($recordArray as $record=>$value){
						
						if(gettype($value) == "object"){
						   $value = array($value);
						}
						
						//->SexOffenseDetail
						foreach($value as $rec){
							
							/*
					        $s = print_r($rec, true);
							echo $s;
							echo "\n";
							
							
							if( isset( $rec->SexOffenseData ) ){
								$hasSexOffender[] = ["check_id"=>$check->id, "name" => $check->first_name . " " . $check->last_name, "detail"=>json_encode($rec->SexOffenseData)];
							}
							 * 
							 */	
						}
					}	
				}
			}
		}  

	  }

      echo "HAS OFFENSES\n";
      $s = print_r($hasOffenses, true);
	  echo $s;
	  echo "\n";
	  
	  echo "HAS SEX OFFENSES\n";
      $s = print_r($hasSexOffender, true);
	  echo $s;
	  echo "\n";
		
	}
	
}