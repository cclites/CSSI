@extends('layouts.app', [
	'title' => $check->full_name,
	'active_menu_item' => 'admin_checks'
])

<?php

	//use Notification;
	use \App\Notifications\SupportRequestEmail;
	use \App\Recipients\InvoiceRecipient;

    $reports = json_decode($check->report());
	$securitec = [3,4,5,6,7];
	$profile = new stdClass();
	
	//$s = print_r($reports, true);
	//Log::info($s);
	
	$testing = false;
	//$testing = true;
	
	if(null !== $check->profile){
		
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		
		if($testing){
			Log::info("****************************************************************************");
			Log::info("***************************** TESTING **************************************");
			Log::info("****************************************************************************");
			Log::info("\n--------------------------- PROFILE --------------------------------------\n");
			
			$s = print_r($profile, true);
			Log::info($s);
			
			Log::info("\n----------------------------- CHECK --------------------------------------\n");
		}
		
	}else{
		Log::info("Profile does not exist for this check");
	}
	
	$types = Cache::get('types');
?>


@section('content')

<div class="row admin-checks-show">
	
    {{-- Needed for editing. --}}
    
    <input type="hidden" id="editAdmin" value="{{ Auth::user()->full_name }}">
    <input type="hidden" id="editDate" value="{{ date("Y-m-d") }}">
    
	<div class="col-md-12 admin-checks-show-summary">

		<div class="panel panel-default col-md-4">
			
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
					<th>For:</th>
					<td><a href="{{ secure_url('/admin/users/' . $check->user->id) }}">{{ $check->user->company_name }}</a></td>
				</tr>
				<tr>
					<th>Name</th>
					<td>{{ $check->full_name }}</td>
				</tr>
				<tr>
					<th>Birthday</th>
					<td>{{ isset($profile->birthday) ? displayDate($profile->birthday) : "N/A" }}</td>
				</tr>
				@if ( isset($profile->ssn) )
					<tr>
						<th>SSN</th>
						<td>{{ isset($profile->ssn) ? displayRedactedSsn($profile->ssn) : "N/A" }}</td>
					</tr>
				@endif
				@if ( isset($profile->license_number)  )
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
				
				@if($check->active == false)
					<tr>
						<th>Deleted</th>
						<td>{{ displayDateTime($check->updated_at) }}</td>
					</tr>
				@endif
			</table>
		</div>

		@if (Auth::user()->hasRole('admin'))
			<div class="panel panel-default col-md-4">
				<div class="panel-body text-center">
					<h1 class="text-large text-danger">
			    		<i class="fa fa-cog" aria-hidden="true"></i>
			    	</h1>
			    	<p class="lead">
			    		Admin Panel
			    	</p>
			    	<p class="text-muted">
			    		This panel is only seen by admins
			    	</p>
			    	<p>
			    		@if (!$check->completed_at)
				    		<a href="{{ secure_url('admin/checks/'.$check->id.'/complete') }}" class="btn btn-lg btn-success">
				    			Mark as Completed
				    		</a>
				    	@endif
				    	@if ($check->completed_at)
				    		<a href="{{ secure_url('admin/checks/'.$check->id.'/incomplete') }}" class="btn btn-lg btn-info">
				    			Mark as Incomplete
				    		</a>
				    	@endif
			    	</p>

					<p>
			    		<a href="{{ secure_url('admin/checks/'.$check->id.'/delete') }}" class="btn btn-lg btn-danger confirm-link"
			    			data-confirm-text="Once a check has been deleted, it's gone. This can not be undone.">
			    			Delete
			    		</a>
			    	</p>
				</div>
				<table class="table">

					@foreach ($check->types as $type)
						<tr>
							<td>
								<a href="{{ secure_url('admin/checks/'.$check->id.'/redo/'.$type->id) }}" class="btn btn-sm btn-default">
									Redo
								</a>
							</td>
							<td>
								<span class="text-{{ $type->color }}">
					    			<i class="fa {{ $type->icon }}" aria-hidden="true"></i>
					    		</span>
					    		{{ $type->title }}
					    		@if ($type->pivot->completed_at)
					    			<br><span class="small text-muted">Completed: {{ displayDate($type->pivot->completed_at) }}</span>
					    		@endif
							</td>
						</tr>
					@endforeach
				</table>
			</div>
		@endif
	</div>
	
    <div class="col-md-12">
    	
	@foreach($reports as $report)

		@php
			$type = $types[$report->check_type - 1];
		@endphp
		
			<div id="{{ $type->slug }}">

				<div class="panel panel-default" data-type="{{ $type->id }}">
					
					@if( $type->id == 1 || $type->id ==2 )
					<button class="btn btn-success report-edit-btn edit-button-row" onclick="Admin.editReport({{ $report->id }})">Edit</button>
					<button class="btn btn-primary report-edit-save-btn edit-button-row" onclick="Admin.saveEditReport({{ $report->id }}, {{ $type->id }})">Save</button>
					<button class="btn btn-danger report-edit-cancel-btn edit-button-row" onclick="Admin.cancelEditReport()">Cancel</button>
					@endif
					
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

		
	</div>
	


</div>
	
@endsection