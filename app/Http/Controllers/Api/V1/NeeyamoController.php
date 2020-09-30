<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Neeyamo;
use App\Models\Report;
use App\Models\Check;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Crypt;

use Log;

use Notification;
use App\Notifications\EducationCheckReadyEmail;
use App\Notifications\EmploymentCheckReadyEmail;
use \App\Notifications\InsufficiencyEmail;

use \App\Recipients\InvoiceRecipient;

/*
 * This is a one-off controller for Neeyamo to receive their push requests and handle the
 * responses.
 */

class NeeyamoController extends Controller{
	
	/*
	 * https://api.eyeforsecurity.com/api/cssi/ney/insufficiency
	 */ 
	public function insufficiency(Request $request){
		
		Log::info("NeeyanoController::insufficiency");
		
		//return json_encode($request->all());
		
		cLog("NeeyamoController::insufficiency", 'app/v1/controllers', 'neeyamo');
		
		$msg = json_encode($request->all());
		cLog($msg, 'app/v1/controllers', 'neeyamo');
		
		try{

			$report = Report::where('provider_id', $request->CaseReferenceNo)->first();
			
			if(is_null($report)){
				
				cLog("There is no report for this insufficiency. EXITING.", 'app/v1/controllers', 'neeyamo');
				
				return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
				
			}else{
				
				cLog("Parsing insufficiency. CONTINUING.", 'app/v1/controllers', 'neeyamo');
				
				$xml = str_replace(array("\r\n", "\r", "\n"), "", $request->Xml);
				$xml = stripslashes($xml);
				$xml = simplexml_load_string($xml);
				
				$xml->insufficientData = true;
				$xml->resultComplete = false;
				
				$message = "Incorrectly parsed insufficiency";
				$applicant = "We have no applicant";
					
				if(isset($xml->InsufficiencyInformation->Insufficiency)){
					cLog("Used the xml->InsufficiencyInformation->Insufficiency path", 'app/v1/controllers', 'neeyamo');
					$message = $xml->InsufficiencyInformation->Insufficiency;
					$applicant = $xml->InsufficiencyInformation->ApplicantFullName;
				}elseif( isset($xml->Insufficiency) ){
					cLog("Used the xml->Insufficiency path", 'app/v1/controllers', 'neeyamo');
					$message = $xml->Insufficiency;
					$applicant = $xml->ApplicantFullName;
				}

				$report->report = $message;
				$report->save();
				
				Log::info("Saved the report");
				
				$recipient = new InvoiceRecipient(env("DEV_EMAIL"));
				Log::info("Have a recipient");
				
				
				$recipient->notify(new InsufficiencyEmail($recipient, $message, $applicant));
				Log::info("Notification succeeded");
	 
				return Response::json(array(
							  		'CaseReferenceNo' => $request->CaseReferenceNo,
						            'IsSuccess' => true),
						            200
						        );
			}
	
	    
		}catch(\Exception $e){
			
			cLog("Insufficiencies Exception", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
	
		}catch (\Throwable $e){
			
			cLog("Insufficiencies Throwable", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
		}
		 
			
			
		Log::info("Jumped past everything. Nothing to return");
	}
	
	/*
	 * https://api.eyeforsecurity.com/api/cssi/ney/status
	 */
	public function status(Request $request){
		
		cLog("NeeyamoController::status", 'app/v1/controllers', 'neeyamo');
		
		$msg = json_encode($request->all());
		cLog($msg, 'app/v1/controllers', 'neeyamo');
		

		try{
			
			$xml = str_replace(array("\r\n", "\r", "\n"), "", $request->Xml);
			$xml = stripslashes($xml);
			$xml = simplexml_load_string($xml);
				
			$report = Report::where('provider_id', $request->CaseReferenceNo)->first();
			
			if(is_null($report)){
				
				cLog("There is no report for this status. EXITING.", 'app/v1/controllers', 'neeyamo');
				
				return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
			}
			
			$status = Neeyamo::parseStatus($request->Xml);
			$report->report = $status;
			$report->save();
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
			
		}catch(\Exception $e){
			
			cLog("Status Exception", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
			
		}catch(\Throwable $e){

			cLog("Status Throwable", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
			
		}
	
	}
	
	/*
	 * https://api.eyeforsecurity.com/api/cssi/ney/result
	 */
	public function result(Request $request){
		
		cLog("NeeyamoController::result", 'app/v1/controllers', 'neeyamo');
		$msg = json_encode($request->all());
		cLog($msg, 'app/v1/controllers', 'neeyamo');
		
		try{
			
			$xml = str_replace(array("\r\n", "\r", "\n"), "", $request->Xml);
			$xml = stripslashes($xml);
			$xml = simplexml_load_string($xml);
			
			$xml->insufficientData = false;
			$xml->resultComplete = true;
			
			//Log::info("xml has been converted from string");
			//Log::info(json_encode($xml));
		
			$report = Report::where('provider_id', $request->CaseReferenceNo)->first();
			
			//I need to set the check as complete
			$check = $report->check;
			
			$checktype = $check->type;
			$checktype->is_completed();
			$checktype->save();
			
			$check->is_completed();
			$check->save();
			
			cLog("Got the report", 'app/v1/controllers', 'neeyamo');
			$rep = json_encode($report);
			cLog($rep, 'app/v1/controllers', 'neeyamo');
			
			if(is_null($report)){
				
				cLog("There is no report for this result. EXITING.", 'app/v1/controllers', 'neeyamo');
				
				return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
				
			}
			
			//Log::info("Got the report");
			//Log::info(json_encode($report));
			
			$result = Neeyamo::parseResult($xml, $report->check_type);
			
			Log::info("Have a result");
			Log::info(json_encode($result));
			
			$report->report = encrypt(json_encode($result));
			$report->save();
			
			Log::info("Saved the report");
			
			$check = Check::find($report->check_id);

			$fee = 0;

			if(isset($xml->VerificationDetail->VerifiedDetail->AdditionalFees)){
				Log::info("Fee field has been set");
				$fee = $xml->VerificationDetail->VerifiedDetail->AdditionalFees;
			}else{
				Log::info("Fee field has not been set");
			}
			
			if($fee){
			    $check->additional_fee = $fee;
			    $check->save();
				Log::info("Fee has been saved");
			}
			
			if($report->check_type == 8){
				$check->user->notify(new EmploymentCheckReadyEmail($check->id));
			}elseif($report->check_type == 9){
				$check->user->notify(new EducationCheckReadyEmail($check->id));
			}

			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
					            'IsSuccess' => true),
					            200
					        );
		
		}catch(\Exception $e){
				
			cLog("Report Exception", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
								'CaseReferenceNo' => $request->CaseReferenceNo,
						  		'data' => json_encode($request->all()),
					            'IsSuccess' => true),
					            200
					        );
			
		}catch(\Throwable $e){
				
			cLog("Report Throwable", 'app/v1/controllers', 'neeyamo');
			cLog($e->getMessage(), 'app/v1/controllers', 'neeyamo');
			
			return Response::json(array(
						  		'CaseReferenceNo' => $request->CaseReferenceNo,
						  		'data' => json_encode($request->all()),
					            'IsSuccess' => true),
					            200
					        );
		}
        
			
	}
	
	
}
