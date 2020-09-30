<?php

namespace App\Http\Controllers;

// Models
use App\Models\Invoice;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


use Log;
use Auth;
use View;
use App;
use Response;

use App\Http\Requests;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller{
	
	protected $il;
	
	public function __construct()
    {
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function index(Request $request)
	{
		return view('invoices/index')
            ->with('request', $request);
	}

	public function show(Request $request, $id, $view = 'html')
	{
		Log::info("InvoiceController::show");
		
	    $invoice = $this->api
	        ->be(auth()->user())
	        ->get('api/invoices/'.$id);
			
			
        if($view == "html"){
        	
        	return view('invoices/_pdf')
	        ->with('invoice', $invoice);
				
        }else if($view == "pdf"){

			 $invoiceHtml = View::make('invoices/pdf', ['invoice' => $invoice])->render();
			 return PDF::loadHTML($invoiceHtml)->setPaper('a4')->inline();
			
        }
	    
	}
	
	public function read($id){
		
		Log::info("InvoiceController::read");
		
		//return $id;
		
		
		//$invoice = $this->api
	        //->get('api/admin/invoices/i/'.$id);
			
			
		//return json_encode($invoice);
			
		/*
		return view('invoices/show')
	        ->with('invoice', $invoice);
		 * 
		 */
		
		$invoice = App\_Models\Invoice::find($id);
		
		if( is_null($invoice) ){
			return "Invoice is null";
		}else{
			return view('invoices/_pdf')
	        ->with('invoice', $invoice);
		}
		
	}

	public function streamInvoice(Request $request){
		
		Log::info("InvoiceController::streamInvoice");
		
		$pdf = App::make('snappy.pdf.wrapper');
		$invoice = \App\_Models\Invoice::find($request->invoiceId);

		$view = View::make('invoices/_pdf', ['invoice' => $invoice])->render();
		
		//Log::info($view);
		
		return $pdf->loadHTML($view)->setPaper('a4')->inline();

        /*
		$il = new App\Http\Controllers\Library\Api\InvoicesLibrary;
		$invoice = \App\_Models\Invoice::find($request->invoiceId);
		
		$stream = $il->streamInvoice($invoice);
		return $stream;
		 * 
		 */
		
	}
	
	

}