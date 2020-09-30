@php

    Log::info("in _pdf blade");
    
	$chargedAmount = 0;
	
	if($invoice->stripe_charge){
		
		$stripeCharge = json_decode($invoice->stripe_charge);
		
		if($stripeCharge->Authorized == "authorized"){
			$chargedAmount = $stripeCharge->Amount;
		}
	}else{
		Log::info("Stripe charge was NOT authorized");
	}
	

	$orders = $invoice->orders;
	$company = $invoice->company;
	
	$totalDue = 0;
	
	foreach($orders as $order){

	    $checks = \App\_Models\Check::where('order_id', $order->id)->get();
	    
	    foreach($checks as $chk){
	    	$totalDue += $chk->amount;
	    }
	
	}
	

	$companyRep = App\Models\User::find($company->company_rep);
	
	$invoiceCreatedDate = $invoice->date;
	$dateTuples = explode('-', $invoiceCreatedDate);
	$invoiceDate = \Carbon\Carbon::createFromDate($dateTuples[0], $dateTuples[1], $dateTuples[2], null);
	$chargeDate = \Carbon\Carbon::createFromDate($dateTuples[0], $dateTuples[1], $dateTuples[2], null)->addDays(2);
	
	$types = cache('types');
	
@endphp


<!-- Main content -->
<section class="invoice">
	
  
  <!-- title row -->
  <div class="row">
    <div class="col-sm-8">
      @if ($whitelabel)
          <img src="{{ secure_url($whitelabel->path.'/images/logos/login.png') }}" class="img-responsive" style="max-width:150px;" alt="logo">
      @else
          <i class="fa fa-3x fa-id-card-o" aria-hidden="true"></i>
          <br>
          <b>{{ env('APP_NAME') }}
      @endif
    </div>
    <div class="col-sm-4">
      <h1>Invoice</h1>
    </div>
    <!-- /.col -->
  </div>

  <hr>

  <!-- info row -->
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      <strong>From</strong>
      <address>
        Corporate Security Solutions, INC<br>
        P.O. Box 950251<br>
        Lake Mary, FL 32795<br>
        Tel: 407-260-1309<br>
        Email: jettore@eyeforsecurity.com
      </address>
    </div>
    <br>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
      <strong>To</strong>
      <address>
        {{ $companyRep->full_name }}<br>
        {{ $company->address }}<br>
        @if ( $company->secondary_address )
          {{ $company->secondary_address }}<br>
        @endif
        {{ $company->city }}, {{ $company->state }} {{ $company->zip }}<br>
        Phone: {{ displayPhone($company->phone) }}<br>
        Email: {{ $company->email }}
      </address>
    </div>
    <br>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
    	
      <h3>
      	<b>
      		<span class="col-md-3">Balance Due:</span>
      	</b> {{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}
      </h3>	
    	
      <b>Invoice #: {{ $invoice->id }}</b><br>
      <b>Invoice Date:</b> {{ displayDate($invoiceDate) }}<br>
      <b>Payment Due:</b> {{ displayDate($chargeDate) }}<br>
    </div>
    
    
    <br><br>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <!-- Table row -->
  <div class="row">
    <div class="col-md-12 table-responsive">
      <table class="table table-striped">
        <thead>
        <!--tr style="padding-bottom: 12px; height: 40px;">
          <th>Date</th>
          <th>Description</th>
          <th class="text-right">Subtotal</th>
        </tr-->
        </thead>
        <tbody>
        	
	        @foreach($orders as $order)
	        
	        	@php
		          $checks = \App\_Models\Check::where('order_id', $order->id)->get();
		        @endphp	
		        
		        @foreach ($checks as $transaction)
		        
		          @php
		            $checkTypeTitle = $types->where('id', $transaction->type)->pluck('title')->first();
		          @endphp
		        
		          <tr>
		          	
		            <td style="padding-right: 12px;">{{ displayDate($transaction->created_at) }}</td>
		            <td>{{ $order->first_name . " " . $order->last_name . " - " . $checkTypeTitle }}</td>
		            <td class="text-right" style="padding-left: 12px;">{{ displayMoney($transaction->amount, 2) }}</td>
		            
		          </tr>
		        @endforeach
		        
		        
	        @endforeach

        </tbody>
      </table>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
  
  <br>
  <br>
  <div class="row">
    <!-- accepted payments column -->
    <div class="col-xs-6">
      Payment Method:
      @if ($company->card_last_four)
          
            {{ $company->card_brand }} ending in {{ $company->card_last_four }}
            <br>
            @if($invoice->stripe_charge == null)
            	Your credit card will be billed automatically
            @else
            	Your credit card has been billed automatically
            @endif
            <br>
      @else
        No payment method on file.<br>
      @endif

      <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
        @if ($invoice->notes)
          {{ $invoice->notes }}
          <br>
        @endif
        Thank you for your business!
      </p>
    </div>
    <!-- /.col -->
    <div class="col-xs-6">
    	

      <b>Amount Due On:</b> {{ displayDate($chargeDate) }}<br>
      <b><span>Subtotal:</span></b>{{ displayMoney(  ($invoice->amount), 2 ) }}<br>
      <b><span>Adjustments:</span></b>{{ displayMoney($invoice->adjustment, 2) }}<br>
      <b><span>Balance Due:</span></b>{{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}<br>
      <b>Charged to Card:</b> {{ displayMoney($chargedAmount, 2)  }}<br>
      <b>Total:</b> {{ displayMoney($invoice->amount - $invoice->adjustment - $chargedAmount, 2) }}<br>
     
      
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>