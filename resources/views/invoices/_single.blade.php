
@php

  $genDate = \Carbon\Carbon::today()->format("Y_m_");
  $companyId = $invoice->company_id;
  $company = \App\_Models\Company::where('id', $companyId)->first();
  
  $orders = \App\_Models\Order::where('company_id', 'sXY2ij')
                                ->whereNotNull('invoice_id')
                                ->get();
  
  $rep = \App\_Models\User::where('id', $company->company_rep)->first();
  
  
  
  
  function createOrderData($order, $checks){
		
		$name = $order->first_name . " " . $order->last_name;
		$orderId = $order->id;
		
		$notes = "Order id: ($orderId) $name, ";
		
		foreach($checks as $check){
				
			$type = cache('types')->where('id', $check->type)->first();

			$title = $type->title;
			$amount = $check->amount;
			$typeId = $check->type;
			
			if($typeId == 3){ //state
			
				$state = json_decode($check->json_data);
				//$state = $json;
			    $title .= " (" . $state->code . ")" . " " . $amount;
			    
			}elseif($typeId == 4){ //county
			
				$county = json_decode($check->json_data);
				//$county = $json;
				$title .= " ( " . $county->title . " " . $county->state_code . " )" . " " . $amount;
				
				
			}elseif($typeId == 6){ //state
			
				$state = json_decode($check->json_data);
				//$state = $json;
				$title .= " (" . $state->code . ")" . " " . $amount;
				
			}elseif($typeId == 7){  //district
			
				$district = json_decode($check->json_data);
				//$district = $json;
				$title .= " ( " . $district->title . " " . $district->state_code . " )" . " " . $amount;
				
			}elseif($typeId == 10){  //mvr
			
				$state = json_decode($check->json_data);
			    //$state = $json;
				$title .= " (" . $state->code . ")" . " " . $amount;
				
			}else{
				$title .= " $amount";
			}
			
			$notes .= $title . ", ";	
		}
		
		return $notes;
	}
  

@endphp

<style>
	

	.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
	  float: left;
	}
	.col-md-12 {
	  width: 100%;
	}
	.col-md-11 {
	  width: 91.66666666666666%;
	}
	.col-md-10 {
	  width: 83.33333333333334%;
	}
	.col-md-9 {
	  width: 75%;
	}
	.col-md-8 {
	  width: 66.66666666666666%;
	}
	.col-md-7 {
	  width: 58.333333333333336%;
	}
	.col-md-6 {
	  width: 50%;
	}
	.col-md-5 {
	  width: 41.66666666666667%;
	}
	.col-md-4 {
	  width: 33.33333333333333%;
	 }
	 .col-md-3 {
	   width: 25%;
	 }
	 .col-md-2 {
	   width: 16.666666666666664%;
	 }
	 .col-md-1 {
	  width: 8.333333333333332%;
	 }
	
	 .section{
	 	font-size: 12px !important;
	 }
	 
	 .invoice-col{
	 	/*font-size: 6px !important*/
	 	
	 }
	 
	 .td{
	 	font-size: 6px !important;
	 }
	
</style>

<!-- Main content -->
<section class="invoice">
  <!-- title row -->
  <div class="row">
  	
    <div class="col-md-8">
      @if ($whitelabel)
          <img src="{{ secure_url($whitelabel->path.'/images/logos/login.png') }}" class="img-responsive" style="max-width:150px;" alt="logo">
      @else
          <i class="fa fa-3x fa-id-card-o" aria-hidden="true"></i>
          <br>
          <b>{{ env('APP_NAME') }}</b>
      @endif
    </div>
    
    
    <!--div class="col-md-4">
    	<a href="{{ secure_url('invoices/' . $invoice->id . '/pdf') }}">Print Invoice</a>
    </div-->
    
    <br>
    
    <div class="col-md-4">
      <h1>Invoice</h1>
    </div>
    <!-- /.col -->
  </div>

  <hr>
  
  <br>

  <!-- info row -->
  <div class="row invoice-info">
  	
    <div class="col-md-12 invoice-col">
      From:
      <address>
        <strong>Corporate Security Solutions, INC</strong><br>
        P.O. Box 950251<br>
        Lake Mary, FL 32795<br>
        Tel: 407-260-1309<br>
        Email: jettore@eyeforsecurity.com
      </address>
    </div>
    
    <!-- /.col -->
    <div class="col-md-12 invoice-col">
      To:
      <address>
      	<strong>{{ $company->company_name }}</strong><br>
        <strong>{{ $rep->first_name . " " . $rep->last_name }}</strong><br>
        {{ $company->address }}<br>
        @if ( $company->secondary_address )
          {{ $company->secondary_address }}<br>
        @endif
        {{ $company->city }}, {{ $company->state }} {{ $company->zip }}<br>
        Phone: {{ displayPhone($company->phone) }}<br>
        Email: {{ $company->email }}
      </address>
    </div>
    <!-- /.col -->
    
    <div class="col-md-12 invoice-col">
      <h3>
      	<b>
      		<span class="col-md-3">Balance Due:</span>
      	</b> {{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}
      </h3>
      
      <b><span class="col-md-3">Invoice #: </span></b>{{ $invoice->id }}<br>
      
      {{-- Calculate the payment due date --}}
      @php
      
        $invoiceDate = \Carbon\Carbon::today()->startOfMonth()->format("M d, Y");
        $paymentDueDate = \Carbon\Carbon::today()->StartOfMonth()->addDays(2)->format("M d, Y");
        	   
	   //***************************************************************************
      
      @endphp
      
      <b><span class="col-md-3">Invoice Date:</span></b>{{ $invoiceDate }}<br>
      <b><span class="col-md-3">Payment Due:</span></b>{{ $paymentDueDate }}<br>
      
            
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
  
  <br>
  <hr>

  <!-- Table row -->
  <div class="row">
    <div class="table-responsive">
      <table class="table ">
        
        <tbody>
        	
        	
        	@foreach($orders as $order)
        	
        	  @php
				$details = createOrderData($order, $order->checks);
        	  @endphp
        	
        	  <tr>
        	  	
        	  	<td class="col-md-3">{{ $order->created_at }}</td>
        	  	<td class="col-md-6">{{ $details }}</td>
        	  	<td class="col-md-3 text-right">$ {{ number_format($order->amount(), 2) }}</td>

        	  </tr>
        	@endforeach

        </tbody>
        
      </table>  

  <div class="row">
    <!-- accepted payments column -->
    <div class="col-xs-12">
    	
      @if ($company->stripe_customer_id)
      <div><b>Payment Method:</b>
      	{{ $company->card_brand }} ending in {{ $company->card_last_four }}
      </div>
      @else
      <br>
      <div>
        <a href="{{ secure_url('settings/billing') }}"><button>Click here to set up convenient automatic payments!</button></a>
      </div>
      @endif
     
      @if ($invoice->notes)
      <div class="col-xs-12 well">{{ $invoice->notes }}</div>
      @endif
      
      <h4>Thank you for your business!</h4>
      
      
      
    </div>
    <!-- /.col -->

    <div class="col-xs-12">
    	
      <b><span>Amount Due On:</span></b>{{ $paymentDueDate }}<br>
      
      <b><span>Subtotal:</span></b>{{ displayMoney(  ($invoice->amount), 2 ) }}<br>
      
      <b><span>Adjustments:</span></b>{{ displayMoney($invoice->adjustment, 2) }}<br>
      
      <b><span>Balance Due:</span></b>{{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}<br>

      
    </div>
    
    <div class="col-xs-12">
    	
    </div>
    
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
