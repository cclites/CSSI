@extends('layouts.app', [
    'title' => 'Billing',
    'active_menu_item' => 'settings',
])


@section('content')

{!! Form::open([ 'id' => 'form-stripe', 'url' => secure_url('settings/billing') ] ) !!}
    {!! Form::hidden('stripeId', Auth::user()->stripe_customer_id) !!}
    {!! Form::hidden('stripeToken', null) !!}
{!! Form::close() !!}

@php

	try{
		
		Log::info(Auth::user()->company_id);
		
		$company = \App\_Models\Company::where('company_id', Auth::user()->company_id)->first();
		
		Log::info(json_encode($company));
		
		$invoices = \App\_Models\Invoice::where('company_id', $company->id)->get();
		
		//Log::info(json_encode($invoices));
		//return;

	}catch(\Exception $e){
		Log::info("Unable to get company.");
	}
	
@endphp

<div class="row user-billing">
	
	{{-- 
    <div class="col-sm-3">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large">
                    <strong>
                        <span class="text-primary">
                            {{ displayMoney( \App\Models\Company::balance( Auth::user()->company_id ) ) }}
                        </span>
                    </strong>
                </h1>
                <p class="text-muted">
                    Current Balance
                </p>
                
                
                 <p class="text-muted">
	            	Adjustments: ${{ !is_null(Auth::user()->adjustment() ) ? Auth::user()->adjustment() : 0.00 }}
	            </p>
                
            </div>
        </div>
    </div>
    --}}
    
    <div class="col-sm-12">
        

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-sm-6 text-center">
                        <p class="">
                            Your balance will be billed automatically on the 3rd day of every month.
                        </p>
                        <br>
                        @if(isset(Auth::user()->card_brand))
                        <p><button id="btn-stripe" class="btn btn-lg btn-primary">Update Credit Card</button></p>
                        @else
                        <p><button id="btn-stripe" class="btn btn-lg btn-primary">Add Credit Card</button></p>
                        @endif
                        
                        {{-- 
                        @if( isset( Auth::user()->stripe_customer_id) && null == isset(Auth::user()->card_brand) )
                          <p><button id="btn-plaid" class="btn btn-lg btn-primary">Update ACH Info</button></p>
                        @else
                          <p><button id="btn-plaid" class="btn btn-lg btn-primary">Add ACH Info</button></p>
                        @endif
                        --}}
                        
                        @if(isset(Auth::user()->card_brand))
                        <p><button id="btn-clear" onclick="Admin.removePaymentInfo({{ Auth::user()->id }})" class="btn btn-lg btn-primary">Remove Payment Option</button></p>
                        @endif
                        
                    </div>

					
                    <div class="col-sm-6">
                        @if (Auth::user()->card_brand)
                            <p class="text-center lead">{{ Auth::user()->card_brand }}</p>
                            <p class="text-center">**** **** **** {{ Auth::user()->card_last_four }}</p>
                            <p class="text-center">Valid Thru {{ Auth::user()->card_expiration }}</p>
                        @elseif(isset( Auth::user()->stripe_customer_id) && null == isset(Auth::user()->card_brand) )
                        
                        @else
                            <p class="text-center">No Payment Method Yet</p>
                            <div class="spacer"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <h4>Recent Invoices</h4>
    </div>
    
    
    <div class="list-group">
    	

    	<!--a href="#" class="list-group-item text-center">
            Invoices are currently unavailable. Please check back later.
        </a-->
        
        
        @if(!$invoices->count())
        	<a href="#" class="list-group-item text-center">
                No recent invoices
            </a>
        @else
        
            @foreach($invoices as $invoice)
        		<br>
	        	<div class="col-md-1">
		        	<a href="{{  secure_url('/invoices/stream/pdf/?invoiceId=') . $invoice->id }}" target="_blank" download="download">
		        		
		        		<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true" style="margin-top: 14px;"></i>
		   
		        	</a>
		        	
		        </div>
				<div class="col-md-11">
		        
		            <a href="{{ secure_url('invoices/'.$invoice->id) }}" target="_blank" class="list-group-item">
		            <!--a href="#" class="list-group-item" disabled="disabled"-->  
		                
		                <h4 class="list-group-item-heading">
		                  <strong>Invoice {{ $invoice->id }}</strong>
		                  <span class="pull-right"><strong>{{ displayMoney($invoice->amount - $invoice->adjustment) }}</strong></span>
		                </h4>
		                <p class="list-group-item-text">
		                    {{ displayDate($invoice->created_at) }}
		                </p>
		                @if ($invoice->notes)
		                    <p class="list-group-item-text">
		                        {{ $invoice->notes }}
		                    </p>
		                @endif
		                
		            </a>
		       </div>
            @endforeach
            
        @endif
    	

        
       
    </div>
</div>

<template id="ach-accept">
	<div class="ach-terms-wrapper">
		<div class="ach-terms">
			<p>
				I authorize CSSI to electronically debit my account and, if necessary, electronically credit my account to correct erroneous debits.
				<br>
				You may revoke authorization at any time by contacting CSSI at 1-800-203-4731.
			</p>
			
			<div class="col-sm-12 text-center">
				<button class="btn btn-primary btn-lg" onclick="achConfirm();">Confirm</button><button class="btn btn-primary btn-lg" onclick="achCancel();">Cancel</button>
			</div>
		</div>
	</div>	
</template>

@endsection


@section('js')
<script src="https://checkout.stripe.com/checkout.js"></script>
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

<script>
var handler = StripeCheckout.configure({
  key: '{{ env('STRIPE_KEY') }}',
  image: '{{ secure_url($whitelabel->path.'/images/logos/icon.png') }}',
  locale: 'auto',
  token: function(token) {
    $('#form-stripe').find('[name=stripeToken]').val(token.id);
    $('#form-stripe').submit();
  }
});

document.getElementById('btn-stripe').addEventListener('click', function(e) {
  // Open Checkout with further options:
  handler.open({
    name: '{{ env('APP_NAME') }}',
    description: 'Payment Method',
    zipCode: true,
    amount: 0,
    email: '{{ Auth::user()->email }}',
    allowRememberMe: false,
    panelLabel: 'Submit Payment Method',
    zipCode: true
  });
  e.preventDefault();
});

// Close Checkout on page navigation:
window.addEventListener('popstate', function() {
  handler.close();
});


var linkHandler = Plaid.create({
  env: 'development',
  clientName: 'CSSI',
  key: '{{ env('PLAID_ACH_PUBLIC') }}',
  product: ['auth'],
  selectAccount: true,
  onSuccess: function(public_token, metadata) {

    Admin.registerAch(public_token, metadata.account_id);
    
  },
  onExit: function(err, metadata) {
  	//alert("PLAIN ON-EXIT")
    // The user exited the Link flow.
    if (err != null) {
      //alert("PLAID ERROR");
      // The user encountered a Plaid API error prior to exiting.
    }
  },
});


function achConfirm(){
	linkHandler.open();
	$(".ach-terms-wrapper").remove();
}

function achCancel(){
	
	$(".ach-terms-wrapper").remove();
}

/*
function streamInvoice(id){
	
		let data = {
				_token : $('meta[name="csrf-token"]').attr('content'),
				invoiceId : id
			},
			
			
		basePath = $("#basePath").val();
		
		$.ajax({
                type : "GET",
                url : basePath + "/invoices/stream",
                success : function(data) {
		    		
					var blob = new Blob([data], {
						type : 'application/pdf'
					});
					
		
					if (window.navigator.msSaveOrOpenBlob) {
						window.navigator.msSaveBlob(blob, "Invoice");
					} else {
						var elem = window.document.createElement('a');
						elem.href = window.URL.createObjectURL(blob);
						elem.download = "Invoice";
						document.body.appendChild(elem);
						elem.click();
						document.body.removeChild(elem);
					}

				},
                error : function(a, b, c) {
			    	$(".progress-overlay").hide(400);
			    	console.log(JSON.stringify(a));
			    	console.log(JSON.stringify(b));
			    	console.log(JSON.stringify(c));
				},
                data : data,
            }).always(function() {
            	//$(".modalWindowCover").fadeOut(400);
            });
	
}
*/

// Trigger the Link UI
/*
document.getElementById('btn-plaid').onclick = function() {
	
	$("body").append( $("#ach-accept").html() );
  
};

*/


</script>
@endsection