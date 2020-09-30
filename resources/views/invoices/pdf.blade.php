@php
	$chargedAmount = 0;
	
	if($invoice->stripe_charge){
		
		$stripeCharge = json_decode($invoice->stripe_charge);
		
		if($stripeCharge->Authorized == "authorized"){
			$chargedAmount = $stripeCharge->Amount;
		}
	}
	
	$totalAdjustments = $chargedAmount;

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
        {{ $invoice->user->full_name }}<br>
        {{ $invoice->user->address }}<br>
        @if ( $invoice->user->secondary_address )
          {{ $invoice->user->secondary_address }}<br>
        @endif
        {{ $invoice->user->city }}, {{ $invoice->user->state }} {{ $invoice->user->zip }}<br>
        Phone: {{ displayPhone($invoice->user->phone) }}<br>
        Email: {{ $invoice->user->email }}
      </address>
    </div>
    <br>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
      <h3><b>Balance Due:</b> {{ displayMoney($invoice->amount - $totalAdjustments, 2) }}</h3>
      
      
      
      <b>Invoice #: </b>46723<br>
      <b>Invoice Date:</b> {{ displayDate($invoice->date) }}<br>
      <b>Payment Due:</b> {{ displayDate($invoice->date) }}<br>
    </div>
    
    <br><br>
    <!-- /.col -->
  </div>
  <!-- /.row -->

  <!-- Table row -->
  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table class="table table-striped">
        <thead>
        <!--tr style="padding-bottom: 12px; height: 40px;">
          <th>Date</th>
          <th>Description</th>
          <th class="text-right">Subtotal</th>
        </tr-->
        </thead>
        <tbody>
        @forelse ($invoice->transactions as $transaction)
          <tr>
            <td>{{ displayDate($transaction->date) }}</td>
            <td style="font-size:12px; padding-left: 25px; padding-bottom: 12px; width: 400px;">{!! displayFormattedText($transaction->description) !!}</td>
            <td class="text-right">{{ displayMoney($transaction->amount, 2) }}</td>
          </tr>
        @empty
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
      <p class="lead"><b>Amount Due:</b> {{ displayDate($invoice->date) }}</p>
      <p><b>Subtotal:</b> {{ displayMoney($invoice->transactions->sum('amount'), 2) }}</p>
      <p><b>Adjustments:</b> {{ displayMoney(0, 2)  }}</p>
      <p><b>Charged to Card:</b> {{ displayMoney($chargedAmount, 2)  }}</p>
      <p><b>Total:</b> {{ displayMoney($invoice->amount - $totalAdjustments, 2) }}</p>

      <!--div class="table-responsive">
        <table class="table">
          <tr>
            <th style="width:50%">Subtotal:</th>
            <td class="text-right">{{ displayMoney($invoice->transactions->sum('amount'), 2) }}</td>
          </tr>
          <tr>
            <th>Adjustments</th>
            <td class="text-right">{{ displayMoney(0, 2)  }}</td>
          </tr>
          <tr>
          	<th>Charged to Card</th>
          	<td class="text-right">{{ displayMoney($chargedAmount, 2)  }}</td>
          </tr>
          <tr>
            <th>Total:</th>
            <td class="text-right">{{ displayMoney($invoice->amount - $totalAdjustments, 2) }}</td>
          </tr>
          
        </table>
      </div-->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>