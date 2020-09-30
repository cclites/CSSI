@extends('layouts.invoice')

@section('content')

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

      <!-- a href="{{ secure_url('invoices/' . $invoice->id . '/pdf') }}">Print Invoice</a -->
 
    </div>
    <!-- /.col -->
  </div>

  <hr>

  <!-- info row -->
  <div class="row invoice-info">

    <div class="col-sm-4 invoice-col">
      <address>
        To: <strong>{{ $invoice->user->full_name }}</strong><br>
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
      <h3><b>Balance Due:</b> {{ displayMoney($invoice->amount, 2) }}</h3>
      <b>Invoice #: </b> {{ $invoice->id }}<br>
      <b>Invoice Date:</b> {{ displayDate($invoice->date) }}<br>
      <b>Payment Due:</b> {{ displayDate($invoice->date) }}<br>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <!-- Table row -->
  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table class="table table-striped">
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
        
	        foreach($invoice->transactions as $transaction){
	        	
	        	//echo "TRANSACTION:\n";
	        	//print_r($transaction);
	        	//echo "\n";
	        	
	        	$typeTuples = explode(",", $transaction->check_type);
	        	
	        	foreach($typeTuples as $t){
	        		
	        		if( in_array($t, [11, 12, 13])) {
		        		$cssiData[$t]["count"] += 1;
			        	$cssiData[$t]["amount"] += $transaction->amount;
		        	}
	        	}	 		
	        }

        @endphp
        	
        @foreach ($invoice->transactions as $transaction)
        
          @if( !in_array( $transaction->check_type, [11, 12, 13] )  )
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
      	@elseif($cssiData["12"]["count"] > 0)
          <tr>
            <td>{{ $cssiData["date"] }}</td>
            <td>Personal Tri-Eye Checks  Count:({{ $cssiData["12"]["count"] }})</td>
            <td class="text-right">{{ displayMoney($cssiData["12"]["amount"], 2) }}</td>
          </tr>
      	@elseif($cssiData["13"]["count"] > 0)
          <tr>
            <td>{{ $cssiData["date"] }}</td>
            <td>Auto Tri-Eye Checks  Count:({{ $cssiData["13"]["count"] }})</td>
            <td class="text-right">{{ displayMoney($cssiData["13"]["amount"], 2) }}</td>
          </tr>
	  	@endif

        @if($invoice->transactions->count() == 0)
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
    <div class="col-xs-6">
      <p class="lead">Payment Method:</p>
      @if ($invoice->user->stripe_customer_id)
          <p class="text-muted">
            {{ $invoice->user->card_brand }} ending in {{ $invoice->user->card_last_four }}
            <br>
            
            @if($invoice->stripe_charge == null)
            	Your credit card will be billed automatically
            @else
            	Your credit card has been billed automatically
            @endif
          </p>
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
      <p class="lead">Amount Due {{ displayDate($invoice->date) }}</p>

      <div class="table-responsive">
        <table class="table">
          <tr>
            <th style="width:50%">Subtotal for this month: </th>
            <td class="text-right">{{ displayMoney($invoice->transactions->sum('amount'), 2) }}</td>
          </tr>
          
          <tr>
            <th>Adjustments: </th>
            
        	<td class="text-right">
               @if( isset($invoice->adjustment) )
                 {{ displayMoney($invoice->adjustment, 2) }}
               @else
                 {{ displayMoney(0, 2) }}
               @endif
            </td>
    
          </tr>
          
          {{--
          <tr>
          	<th>Charged to Card: </th>
          	<td class="text-right">{{ displayMoney($chargedAmount, 2) }}</td>
          </tr>
          --}}
          
          <tr>
            <th>Total:</th>
            <td class="text-right">{{ displayMoney($invoice->amount, 2) }}</td>
            {{-- 
            <td class="text-right">{{ displayMoney($invoice->amount - $totalAdjustments, 2) }}</td>
            --}}
          </tr>
          
        </table>
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
@endsection