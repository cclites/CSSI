@extends('layouts.app', [
    'title' => 'Invoices',
    'active_menu_item' => 'invoice_management',
])

@php

  $allInvoices = \App\_Models\Invoice::all();
  
  $invoices = [];

  foreach($allInvoices as $invoice){
  	
  	$dataObj = [
  		'company_id' => $invoice->company->company_id,
  		'company_name' => $invoice->company->company_name,
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

<div class="panel panel-default">
	
    <div class="panel-body">
        <h4>Manage Invoices</h4>
    </div>
    
    {{--
    <a href="#" class="list-group-item text-center">
        Invoices are currently unavailable. Please check back later.
    </a>
    --}}
    
     
    <table class="table">
    	
    	<tr>
    		<th>Download PDF</th>
    		<!--th style="text-align: center;">Regenerate</th-->
    		<th>&nbsp;</th>
    		<th style="text-align: center;">Reconcile</th>
    		<th>Amount</th>
    		<th>Adjustment</th>
    		<th>Minimum</th>
    		<th>Reconcile Date</th>
    		<th>Reconciled By</th>
    	</tr>
    	
		@forelse($invoices as $invoice)
		
			@php
	    
		      $dateCreated = $invoice["invoice"]->created_at;
		      $dateCreated = new \Carbon\Carbon($dateCreated);
		      
		      $invoiceId = $invoice['invoice']->id;
		      $yearMonth = $invoice['invoice']->date;
		      $companyName = $invoice["company_name"];
		      $companyId = $invoice["company_id"];
		      
		      $filePrefix = $dateCreated->format("Y_m_");
		      
		      $file = $filePrefix . $companyId;
		      
		    @endphp
	    
	    <tr class="{{ $invoiceId }}_inv">
			<td>
				<a href='{{ secure_url("/invoices/stream/pdf/?invoiceId=") . $invoice["invoice"]["id"] }}' download="download" disabled>
            		<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"style="margin-top: 11px;"></i>&nbsp;
            	</a>
			</td>
			
			{{-- 
			<td style="text-align: center;">
				<button onclick='regenerateInvoice({{ $invoice["invoice"]["id"] }});' class="btn btn-danger" style="margin-top: 15px;">Ok</button>
			</td>
			--}}
			
			<td>
				<a href="{{ secure_url('/admin/invoices/i/'. $invoiceId) }}" class="list-group-item" target="_blank">
					{{ $yearMonth . " " . $companyName }}
					  <br>
					Invoice Id: {{ $invoiceId }}
				</a>
			</td>
			
			<td style="text-align: center;">
				@php
				  $checked = "";
				  
				  if($invoice['invoice']->reconciled){
				  	$checked = "checked";
				  }
				@endphp
				
				<input type="checkbox" class="reconciled" onchange="Admin.reconcileInvoice({{ $invoiceId }});" {{ $checked }}>
			</td>
			
			<td class="invoice-amount">
				<input data-id="{{ $invoice['invoice']->id }}" onkeyup="updateInvoiceAmount(this)" value="{{ $invoice['invoice']->amount }}">
			</td>
			
			<td class="invoice-adjustment">
				<input data-id="{{ $invoice['invoice']->id }}" onkeyup="updateAdjustmentAmount(this)" value="{{ $invoice['invoice']->adjustment }}">
			</td>
			
			<td class="invoice-minimum">
				<input data-id="{{ $invoice['invoice']->id }}" onkeyup="updateMinimumAmount(this)" value="{{ $invoice['invoice']->minimum }}">
			</td>
			
			
			<td class="reconciled_date">{{ $invoice['invoice']->reconciled_date }}</td>
			<td class="reconciled_by">{{ $invoice['invoice']->reconciled_by }}</td>
	    </tr>
    	
    	
    	@empty
            <tr>
            	<td colspan="6">
            		<h4>No Recent Invoices</h4>
            	</td>
            	
            </tr>
        @endforelse
    	
    </table>    
 
</div>

<script>

	function regenerateInvoice(id){
		
		
		swal({
			title : "Are you sure?",
			text : "Are you sure you want to regenerate this invoice?",
			type : "warning",
			showCancelButton : true,
			confirmButtonColor : "#DD6B55",
			confirmButtonText : "Yes, I'm sure",
			closeOnConfirm : true
		}, function() {
			
			let data = {
					_token : $('meta[name="csrf-token"]').attr('content'),
					invoiceId : id
				},
			    success = function(data) {
			    	
			    	if(data.error){
			    		alert("There was an error updating this check.");
			    	}else{
			    		alert("The invoice has been regenerated.");
			    	}
			    	//console.log(data);
			    	
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
			
		});

	}
	
	function updateInvoiceAmount(self){
			

		var data = {
            val: $(self).val(),
            invoiceId: $(self).data("id"),
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/admin/settings/updateInvoiceAmount",
		    type = "POST",
		    success = function(data) {
			console.log(data);
		},
		    failure = function(a, b, c) {
			console.log("Failed to update invoice amount");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
		
	}
	
	function updateAdjustmentAmount(self){
		
		var val = $(self).val();
		
		var data = {
            val: $(self).val(),
            invoiceId: $(self).data("id"),
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/admin/settings/updateAdjustmentAmount",
		    type = "POST",
		    success = function(data) {
			console.log(data);
		},
		    failure = function(a, b, c) {
			console.log("Failed to update adjustment amount");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	}
	
	function updateMinimumAmount(self){
		
		var data = {
            val: $(self).val(),
            invoiceId: $(self).data("id"),
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/admin/settings/updateMinimumAmount",
		    type = "POST",
		    success = function(data) {
			console.log(data);
		},
		    failure = function(a, b, c) {
			console.log("Failed to update minimum amount");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
		
	}
	
</script>


@endsection