<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;
use \App\Models\Mvr;
use App\Models\Check;
use SoapClient;


// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;

class ReportController extends Controller
{
	public function index(Request $request)
	{
		Log::info("ReportController::index");
		
		return view('reports/index')
            ->with('request', $request);
	}
	
	//Used for federal checks
	public static function sendReportOrder($url, $payload, $headers){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); // specify URL
        curl_setopt($ch, CURLOPT_POST, 1); // Use POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // this will have it return a string on exec call
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // time to allow it to connect
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // time to allow it to send
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		
		
		$response = curl_exec($ch); // perform the post
        curl_close($ch); // close the session
        
        return $response;
		
	}

	public static function sendReportOrderSoap($envelope, $action, $sandbox){

		$wsdl = !$sandbox ? env("SAMBA_WSDL") : env("SAMBA_WSDL_SANDBOX");
		
		$curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $wsdl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 300,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $envelope,
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: text/xml",
            "SOAPAction: http://adrconnect.mvrs.com/adrconnect/2013/04/IAdrConnectWebService/" . $action
          ),
        ));

        $response = curl_exec($curl);
		curl_close($curl);
		return $response;
		

	}
	
	/*
	public static function sendReportOrderSoap($envelope, $action, $wsdl){


		$curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $wsdl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 300,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $envelope,
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: text/xml",
            "SOAPAction: http://adrconnect.mvrs.com/adrconnect/2013/04/IAdrConnectWebService/" . $action
          ),
        ));

        $response = curl_exec($curl);
		curl_close($curl);
		return $response;
		

	}
	 * 
	 */
	
	public function companyReport(Request $request){
		
		Log::info("Requesting company report");
		
		if (!$request->all()) {
            return json_encode([]);
        }

        try {
            return $this->api
            		->be(auth()->user())
            		->with($request->all())
            		->get('api/company/report');
        } catch (UpdateResourceFailedException $e) {
            return back()
            		->withErrors($e->getErrors())
            		->withInput($request->all());
        }
		
	}
	
	public function limitedAdminCompanyReport(Request $request){
		
		Log::info("In ReportController/limitedAdminCompanyReport");
		
		try {
            return $this->api
            		->be(auth()->user())
            		->with($request->all())
            		->get('api/report/limitedAdminCompanyReport');
        } catch (UpdateResourceFailedException $e) {
            return back()
            		->withErrors($e->getErrors())
            		->withInput($request->all());
        }
		
	}

	
	

}