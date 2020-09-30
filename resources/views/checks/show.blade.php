@extends('layouts.app', [
	'title' => $check->full_name,
	'active_menu_item' => 'checks'
])

<?php

	//use Notification;
	use \App\Notifications\SupportRequestEmail;
	use \App\Recipients\InvoiceRecipient;

	$securitec = [3,4,5,6,7];
	$profile = new stdClass();
	$types = Cache::get('types');
	
	if(null !== $check->profile){
		$profile = json_decode(Crypt::decrypt($check->profile->profile));	
	}else{
		Log::info("Profile does not exist for this check");
	}
	
	DB::table("checks")->where('id', $check->id)->update(['viewed'=>1]);
	
?>


@section('content')

<link rel="stylesheet" href="{{ public_path('css/vendor/bootstrap-3.3.7.min.css') }}" media="print">
<link rel="stylesheet" href="{{ public_path('css/vendor/font-awesome.css') }}" media="print">
<link rel="stylesheet" href="{{ public_path('css/vendor/skin-blue-light.min.css') }}" media="print">
<link rel="stylesheet" type="text/css" href="{{ secure_url('css/printChecks.css') }}" media="print">



<div class="col-md-12">

    <!--button onclick="window.print();" class="btn btn-success checks-print">
		<i class="fa fa-print" aria-hidden="true"></i>
		&nbsp;Print Check
	</button-->
	
	<a href="{{ $check->id}}/pdf" target="_blank">
		<button class="btn btn-success checks-print">
			<i class="fa fa-download" aria-hidden="true"></i>
			&nbsp;Download PDF
		</button>
	</a>
	

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-body text-center">
					<h1 class="text-large text-primary">
			    		<i class="fa fa-user-circle" aria-hidden="true"></i>
			    	</h1>
			    	<p class="lead">
			    		Subject Information
			    	</p>
				</div>
				<table class="table table-bordered">
					
					<tr>
						<th>Name</th>
						<td>{{ $check->full_name }}</td>
					</tr>
					<tr>
						<th>Birthday</th>
						<td>{{ isset($profile->birthday) ? displayDate($profile->birthday) : "N/A" }}</td>
					</tr>
					@if ( isset($profile->ssn))
						<tr>
							<th>SSN</th>
							<td>{{ isset($profile->ssn) ? displayRedactedSsn($profile->ssn) : "N/A" }}</td>
						</tr>
					@endif
					@if ( isset($profile->license_number) )
						<tr>
							<th>License Number</th>
							<td>{{ isset($profile->license_number) ? $profile->license_number : "N/A" }}</td>
						</tr>
					@endif
					<tr>
						<th>Type(s)</th>
						<td>
							
							@foreach($check->checktypes as $type)
							  {{ $type->type->title }} <br>
							@endforeach
	
					   </td>
					</tr>
					<tr>
						<th>Date Ordered</th>
						<td>{{ displayDateTime($check->created_at) }}</td>
					</tr>
	
					@if ($check->completed_at)
						<tr>
							<th>Date Completed</th>
							<td>{{ displayDateTime($check->completed_at) }}</td>
						</tr>
					@endif
				</table>
			</div>
		</div>
	
	</div>
</div>

<div class="col-md-12">
	
	<?php
	  //Log::info($check->report);
	?>
    	
@foreach($check->report as $report)

	@php
		$type = $types[$report->check_type-1];
		//$type = $types->where("id", $report->check_type );
		//$type = \App\Models\Checktype::where("id", $report->check_type);
	@endphp
	
		<div id="{{ $type->slug }}">
			<div class="panel panel-default" data-type="{{ $type->id }}">
				
				<div class="panel-body">
					
					<h1 class="text-large text-center">
						<span class="text-{{ $type->color }}">
			    			<i class="fa {{ $type->icon }}" aria-hidden="true"></i>
			    		</span>
			    	</h1>
			    	
			    	<p class="lead text-center">			    		
			    		{!! $type->title !!}			    		
			    	</p>
			    	
			    	@php

			    		try{
				    	    if( $report->check_type == 1 || $report->check_type == 2){
				    	    		
				    		@endphp
				    			@include("templates.data_divers_template", ["report", json_decode($report->report)])
				    		@php    
				    	
				    	    }elseif( in_array($report->check_type, $securitec) ){
				    	    	
				    	    @endphp
				    			@include("templates.securitec_template", ["report", json_decode($report->report)])
				    		@php
				    	    	 	
				    	    }elseif( $report->check_type ==  8){
				    	    	
			    	    	@endphp
				    			@include("templates.employment_template", ["report", json_decode($report->report)])
				    		@php

				    	    }elseif( $report->check_type ==  9){
				    	    
				    	    @endphp
				    			@include("templates.education_template", ["report", json_decode($report->report)])
				    		@php	
				    	    	
				    	    }elseif( $report->check_type == 10 ){
				    	    	
				    	    	@endphp
					    			@include("templates.mvr_template", ["report", $report->report])
					    		@php
	
				    	    }elseif( $report->check_type == 11 || $report->check_type == 12 || $report->check_type == 13){
				    	    	
				    	    	echo '<h4 class="text-center">Checks are only available from the API</h4>';
				    	    	
				    	    }else{
				    	    	
				    	    	echo '<h4 class="text-center">Nothing to display.</h4>';
				    	    	
				    	    }
				    	  
				    	}catch(\Exception $e){
				    		echo '<h4 class="text-center">There is a problem displaying your check. A support ticket has been automatically generated, and you will be contaced once the issue is resolved.</h4>';
				    		
				    		$check->errorType = "Unable to display report " . $report->id;
				    		
				    		//now we need to send an email.
				    		$recipient = new InvoiceRecipient(env('DEV_EMAIL'));
				    		$recipient->notify(new SupportRequestEmail($check));
				    	}
						
			    	@endphp

					
					
				</div>
			</div>
		</div>
	@endforeach
	
    @include('layouts.footer')

	
</div>

@endsection