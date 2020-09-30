<?php
	$securitec = [3,4,5,6,7];
	
	$profile = new stdClass();
	
	if(null !== $check->profile){
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		//Log::info(json_encode($profile));		
	}else{
		Log::info("Profile does not exist for this check");
	}
	
	$types = Cache::get('types');
	
?>


<link rel="stylesheet" href="{{ public_path('css/vendor/bootstrap-3.3.7.min.css') }}">
<link rel="stylesheet" href="{{ public_path('css/vendor/font-awesome.css') }}">
<link rel="stylesheet" href="{{ public_path('css/vendor/skin-blue-light.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<style>
	html, body {
	  width: 260mm;
	  /*height: 297mm;*/
	  font-size: 11px;
	  margin: 0 -6px;
	}
    
	aside.main-sidebar,
	header
	.checks-print {
		display: none;
	}
	
	.wrapper{
		background-color: #fff;
	}
	
	.content{
		/*margin-left: 12px;*/
	}
	
	.report_template h5,
	.report_template h4,
	.line:nth-of-type(2n+1) {
		background-color: #eee;
	}
	
	
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
	
    .report_template h5{
    	font-size: 12px;
    }
    
    .admin-meta-info-wrapper,
	.edit_control_wrapper,
	.report-edit-save-btn,
	.report-edit-cancel-btn{
		display: none;
	}
	
	.report_template h4{
		color: #fff;
		background-color: #00a65a;
		text-align: center;
		height: 40px;
		line-height: 40px;
	}
	
	.report_template .panel-container{
		height: auto;
		overflow:hidden;
	}
	
	.report_template h5{
		background-color: #f39c12;
		height: 20px;
		line-height: 20px;
		padding-left: 8px;
	}
	
	div .minor-subcategory {
		background-color: #3c8dbc;
		height: 20px;
		padding-left: 6px;
		padding-top: 2px;
	}

	div .line{
		border-bottom: 1px solid #eee;
		height: auto;
		overflow:hidden;
	}
	
</style>


<div class="col-md-12">

	<div class="row">
		<div class="col-md-12">
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

			    	@if( $report->check_type == 1 )
						@include("templates.data_divers_template", ["report", json_decode($report->report)])
					@elseif( $report->check_type == 2 )
						@include("templates.data_divers_template", ["report", json_decode($report->report)])
					@elseif( in_array($report->check_type, $securitec) )
						@include("templates.securitec_template", ["report", json_decode($report->report)])
					@elseif( $report->check_type ==  8)
					  @include("templates.employment_template", ["report", json_decode($report->report)])
					@elseif( $report->check_type ==  9)
					  @include("templates.education_template", ["report", json_decode($report->report)])
					@elseif( $report->check_type == 10 )
					  @include("templates.mvr_template", ["report", $report->report])
					@elseif( $report->check_type == 11 || $report->check_type == 12 || $report->check_type == 13)
						<h4 class="text-center">Checks are only available from the API</h4>
					@endif
					
					
				</div>
			</div>
		</div>
	@endforeach
	
	@include('layouts.footer')

	
</div>
