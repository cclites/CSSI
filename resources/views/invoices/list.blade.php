@extends('layouts.app')

@php

  $allInvoices = \App\Models\Invoice::all();
  
  $invoices = [];
  
  //echo "Invoice count is " . $allInvoices->count() . "\n";

  foreach($allInvoices as $invoice){
  	
  	$dataObj = [
  		'company_id' => $invoice->user->company_id,
  		'company_name' => $invoice->user->company_name,
  		'invoice' => $invoice
  	];
  	
    $invoices[] = $dataObj;
  }
  
  
  usort($invoices, "cmp");
  
  function cmp($a, $b)
  {
    return strcmp($a["company_name"], $b["company_name"]);
  }
  

@endphp


@section('content')

<h2>Download Invoices</h2>

<ul>
	
@foreach($invoices as $invoice)

    
	<li>
		
		<button onclick='regenerateInvoice({{ $invoice["invoice"]["id"] }});'>Regenerate Invoice</button>
		
		<a href='{{  secure_url("/invoices/stream/pdf/?invoiceId=") . $invoice["invoice"]["id"] }}' target="_blank" download="download">Download: {{ $invoice["invoice"]["id"] }}</a>

	</li>
		
@endforeach

</ul>

<script>

	function regenerateInvoice(id){
		
		let data = {
				_token : $('meta[name="csrf-token"]').attr('content'),
				invoiceId : id
			},
		    success = function(data) {
		    	
		    	console.log(data);
		    	
			},
		    failure = function(a, b, c) {
		    	console.log(JSON.stringify(a));
		    	console.log(JSON.stringify(b));
		    	console.log(JSON.stringify(c));
			},
		    url = "/admin/invoices/regenerate",
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);
		    
		su.asynch(request);
		
		
	}
	
</script>

@endsection