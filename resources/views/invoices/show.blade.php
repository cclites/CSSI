@extends('layouts.invoice')


@section('content')

<style>
	table tr td:nth-of-type(1){
		width: 126px;
	}
	
	table tr td:nth-of-type(2){
		width: 350px;
		padding: 5px;
	}
	
	table tr td:nth-of-type(3){
		width: 100px;
	}
	
	button{
		border: 1px solid #dd4b39;
		border-radius: 4px;
		color: #fff;
		background-color: #dd4b39;
		cursor: pointer;
		height: 32px;
		font-size: 18px;
	}
	
	
</style>

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
    
    
    <!--div class="col-sm-4">
    	<a href="{{ secure_url('invoices/' . $invoice->id . '/pdf') }}">Print Invoice</a>
    </div-->
    
    <br>
    
    <div class="col-sm-4">
      <h1>Invoice</h1>
    </div>
    <!-- /.col -->
  </div>

  <hr>
  
  <br>

  <!-- info row -->
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      From:
      <address>
        <strong>Corporate Security Solutions, INC</strong><br>
        P.O. Box 950251<br>
        Lake Mary, FL 32795<br>
        Tel: 407-260-1309<br>
        Email: jettore@eyeforsecurity.com
      </address>
    </div>
    <br>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
      To:
      <address>
        <strong>{{ $invoice->user->full_name }}</strong><br>
        {{ $invoice->user->address }}<br>
        @if ( $invoice->user->secondary_address )
          {{ $invoice->user->secondary_address }}<br>
        @endif
        {{ $invoice->user->city }}, {{ $invoice->user->state }} {{ $invoice->user->zip }}<br>
        Phone: {{ displayPhone($invoice->user->phone) }}<br>
        Email: {{ $invoice->user->email }}
      </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
      <h3><b><span style="display: inline-block; width: 180px;">Balance Due:</span></b> {{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}</h3>
      
      <b><span style="display: inline-block; width: 142px;">Invoice #: </span></b>{{ $invoice->id }}<br>
      
      {{-- Calculate the payment due date --}}
      @php
        $paymentDueDate = new \Carbon\Carbon($invoice->date); 
        $paymentDueDate = $paymentDueDate->addDays(9);
        $paymentDueDate = $paymentDueDate->format("M d, Y"); 
      @endphp
      
      <b><span style="display: inline-block; width: 142px;">Invoice Date:</span></b>{{ displayDate($invoice->date) }}<br>
      <b><span style="display: inline-block; width: 142px;">Payment Due:</span></b>{{ $paymentDueDate }}<br>
      
            
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
  
  <br>
  <hr>

  <!-- Table row -->
  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table class="table table-striped checks-table">
        <!--thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th class="text-right">Subtotal</th>
        </tr>
        </thead-->
        <tbody>
        
        @php
        
            $date = \Carbon\Carbon::now();
            $date = $date->subMonth()->format('m/Y');

            $cssiData["date"] = $date;
            $cssiData["11"]["count"] = 0;
            $cssiData["11"]["amount"] = 0;
            $cssiData["12"]["count"] = 0;
            $cssiData["12"]["amount"] = 0;
            $cssiData["13"]["count"] = 0;
            $cssiData["13"]["amount"] = 0;
            
            $il = new \App\Http\Controllers\Library\Api\InvoicesLibrary;
            //$transactions = $il->getTransactions($invoice->user->companyId, $invoice->id);
            $transactions = $invoice->transactions;
            
            //Log::info($transactions->count());
            
	        foreach($transactions as $transaction){
	        	
	        	$typeTuples = explode(",", $transaction->check_type);
	        	
	        	foreach($typeTuples as $t){
	        		
	        		if( in_array($t, [11, 12, 13])) {
	        			//echo "In array $t<br>";
		        		$cssiData[$t]["count"] += 1;
			        	$cssiData[$t]["amount"] += $transaction->amount;
		        	}else{
		        		//echo "Not in array<br>";
		        	}
	        	}	 		
	        }
	        
	    //print_r($cssiData);
	        

        @endphp
        	
        @foreach ($transactions as $transaction)

          @php
            $ckTypes = explode(',', $transaction->check_type);
            
            $hasCssiData = false;
            
            foreach($ckTypes as $ct){
            	if(in_array($ct, [11, 12, 13])){
            		$hasCssiData = true;
            	}
            }
          @endphp
          
        
          @if( !$hasCssiData  )
	          <tr>
	            <td>{{ displayDate($transaction->date) }}</td>
	            <td>{!! displayFormattedText($transaction->description) !!}</td>
	            <td class="text-right">{{ displayMoney($transaction->amount, 2) }}</td>
	          </tr>
          @endif
          
        @endforeach

        @if($cssiData["11"]["count"] > 0)
          <tr>
            <td>{{ $cssiData["date"] }}</td>
            <td>Home & Auto Tri-Eye Checks  Count:({{ $cssiData["11"]["count"] }})</td>
            <td class="text-right">{{ displayMoney($cssiData["11"]["amount"], 2) }}</td>
          </tr>
        @endif  
          
      	@if($cssiData["12"]["count"] > 0)
          <tr>
            <td>{{ $cssiData["date"] }}</td>
            <td>Personal Tri-Eye Checks  Count:({{ $cssiData["12"]["count"] }})</td>
            <td class="text-right">{{ displayMoney($cssiData["12"]["amount"], 2) }}</td>
          </tr>
        @endif
        
      	@if($cssiData["13"]["count"] > 0)

          <tr>
            <td>{{ $cssiData["date"] }}</td>
            <td>Auto Tri-Eye Checks  Count:({{ $cssiData["13"]["count"] }})</td>
            <td class="text-right">{{ displayMoney($cssiData["13"]["amount"], 2) }}</td>
          </tr>
	  	@endif

        @if($transactions->count() == 0)
          <tr>
            <td colspan="3">
              No transaction details
            </td>
          </tr>
        @endforelse
        
        </tbody>
      </table>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <div class="row">
    <!-- accepted payments column -->
    <div class="col-xs-12">
    	
      @if ($invoice->user->stripe_customer_id)
      <div><b>Payment Method:</b>
      	{{ $invoice->user->card_brand }} ending in {{ $invoice->user->card_last_four }}
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
    	
      <b><span style="display: inline-block; width: 142px;">Amount Due On:</span></b>{{ $paymentDueDate }}<br>
      
      <b><span style="display: inline-block; width: 142px;">Subtotal:</span></b>{{ displayMoney(  ($invoice->amount), 2 ) }}<br>
      
      <b><span style="display: inline-block; width: 142px;">Adjustments:</span></b>{{ displayMoney($invoice->adjustment, 2) }}<br>
      
      <b><span style="display: inline-block; width: 142px;">Balance Due:</span></b>{{ displayMoney($invoice->amount - $invoice->adjustment, 2) }}<br>

      
    </div>
    
    <div class="col-xs-12">
    	
    </div>
    
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
@endsection