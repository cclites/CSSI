@extends('layouts.app', [
	'title' => 'Run a Check',
    'active_menu_item' => 'create_check',
])


@section('content')

@php

	$map = [];

	try{
		
		$checks = \App\Models\Check::where('company_id', Auth::user()->company_id)->where('active', true)->orderBy('last_name')->get();

		foreach($checks as $check){
			
			$name = $check->first_name . " " . $check->last_name;
			
			if( !isset($map[$name]) ){
				
				$map[$name] = [
				  'name'=>$name,
				  'check_id'=>$check->id,
				  'viewable'=>$check->viewable,
				  //'dob'=>$dob
				];
				
			}
			
		}

	}catch(\Exception $e){
		Log::info("Error " . $e->getMessage());
	}
	
@endphp


@if(Auth::user()->hasRole('admin'))
<div class="col-md-8 col-md-offset-2 profile_select_container">
	<div class="profile_select_wrapper text-right">
		<select class="profile_select">
			<option value="null" selected><span class="text-muted">Select A Profile</span></option>
			
			@foreach($map as $m=>$obj)
			
			    @if( (!Auth::user()->company_rep && $obj->viewable) || Auth::user()->company_rep )
			    	<option value="{{ $obj['check_id'] }}">{{ $obj['name'] }}</option>
			    @endif
			    
			@endforeach
		</select>
	</div>
</div>
@endif

{!! Form::open(['url' => secure_url('checks'), 'id'=>'input_form']) !!}

@php

	if( !isset($request->types) ){
		echo '<script>window.location.href = "' . secure_url("/checks/create") .'";</script>';
		return;	
	}

@endphp

@foreach ($request->types as $type)
    {!! Form::hidden('check_types[]', $type) !!}    
@endforeach

<div class="row">
    <div class="col-md-8 col-md-offset-2">
    	
    	<input type="hidden" id="distinct" value="{{ createSeed(10) }}"> 
    	
    	@php
    	
    	    $isFederal = false;
    	    $securitec = [3,4,5,6,7];
    	
    	    foreach ($request->types as $type){
    	    	
    	    	if( in_array($type, $securitec) ){
    	    		$isFederal = true;
    	    	}
    	    	
    	    }
    	  
    	@endphp

        <div class="panel panel-default">
            <div class="panel-body">
            	@if($isFederal)
            	  <div class="text-danger">Federal checks often require a minimum of 24 hours to complete. You will receive an email when the check is ready.</div>
            	@endif
            	
            	
            	@if(Auth::user()->hasRole('admin'))
            	
            	<div class="row text-right hide_check_selector">
            		<span><label>Hide from Employees</label>&nbsp;<input type="checkbox" name="hide_check" value="false">&nbsp;</span>
            	</div>
            	
            	@endif
            	
                <h1 class="text-large text-primary text-center">
                    <i class="fa fa-user-circle" aria-hidden="true"></i>
                </h1>
                <p class="lead text-center">
                    Subject Information
                </p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('first_name', 'First Name') !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('middle_name', 'Middle Name') !!}
                            {!! Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => 'Middle Name']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('last_name', 'Last Name') !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Last Name']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4">    
                        <div class="form-group">
                            {!! Form::label('birthday', 'Birthday') !!}
                            {{-- Form::text('birthday', null, ['class' => 'form-control', 'placeholder' => 'MM/DD/YYYY']) --}}
                            <input type="date" class="form-control date-selection" name="birthday" id="birthday" value="" placeholder="'MM/DD/YYYY'">
                        </div>
                    </div>
                    @if ( !empty(array_intersect([
                            1,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9,
                            11,
                            12,
                        ], $request->types)
                    ))
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('ssn', 'Social Security Number') !!}
                                {!! Form::text('ssn', null, ['id' => 'ssn', 'class' => 'form-control', 'placeholder' => 'XXX-XX-XXXX']) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if (in_array(1, $request->types) || in_array(2, $request->types))
                        <div class="col-md-4">
                            <div class="form-group">
                            	{!! Form::label('range', 'Date Range') !!}
                            	<select id="range" class="form-control">
                            		<option value="">No Date Range</option>
                            		<option value="3">Last 3 years</option>
                            		<option value="7">Last 7 years</option>
                            	</select>
                            </div>
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>


        @if (in_array(3, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(3)->color }} text-center">
                        <i class="fa {{ cache('types')->find(3)->icon }}" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        {{ cache('types')->find(3)->title }} Information
                    </p>
            
                    <div class="form-group">
                        {!! Form::label('state_tri_eye_state_ids[]', 'State Check') !!}
                        {!! Form::select('state_tri_eye_state_ids[]', cache('states')->pluck('title_with_extra_cost', 'id'), null, ['class' => 'form-control', 'multiple', 'data-live-search' => 'true']) !!}
                    </div>
            
                </div>
            </div>
        @endif

        @if (in_array(4, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(4)->color }} text-center">
                        <i class="fa {{ cache('types')->find(4)->icon }}" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        {{ cache('types')->find(4)->title }} Information
                    </p>
            
                    <div class="form-group">
                        {!! Form::label('county_tri_eye_state', 'County Check State') !!}
                        {!! Form::select('county_tri_eye_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
                    </div>


                    <div class="form-group">
                        {!! Form::label('county_tri_eye_county_ids[]', 'County Check') !!}
                        {!! Form::select('county_tri_eye_county_ids[]', [], null, ['class' => 'form-control', 'multiple', 'data-live-search' => 'true', 'id' => 'county_tri_eye_county_ids']) !!}
                    </div>
            
                </div>
            </div>
        @endif

        @if (in_array(6, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(6)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(6)->icon }}" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        {{ cache('types')->find(6)->title }} Information
                    </p>
            
                    <div class="form-group">
                        {!! Form::label('federal_state_tri_eye_state_ids[]', 'Federal State') !!}
                        {!! Form::select('federal_state_tri_eye_state_ids[]', cache('states')->pluck('title', 'id'), null, ['class' => 'form-control', 'multiple', 'data-live-search' => 'true']) !!}
                    </div>
            
                </div>
            </div>
        @endif

        @if (in_array(7, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(7)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(7)->icon }}" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        {{ cache('types')->find(7)->title }} Information
                    </p>
            
                    <div class="form-group">
                        {!! Form::label('federal_district_tri_eye_district_ids[]', 'Federal District') !!}
                        {!! Form::select('federal_district_tri_eye_district_ids[]', cache('districts')->pluck('state_code_with_title', 'id'), null, ['class' => 'form-control', 'multiple', 'data-live-search' => 'true']) !!}
                    </div>
            
                </div>
            </div>
        @endif


        @if (in_array(8, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(8)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(8)->icon }}" aria-hidden="true"></i>
                    </h1>
                    
            
                    <p class="lead text-center">
                        Current Employment Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_name', 'Employer Name') !!}
                                {!! Form::text('employment_current_employer_name', null, ['class' => 'form-control', 'placeholder' => 'Employer Name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_phone', 'Employer Phone') !!}
                                {!! Form::text('employment_current_employer_phone', null, ['class' => 'form-control', 'placeholder' => 'Employer Phone']) !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_address', 'Employer Address') !!}
                                {!! Form::text('employment_current_employer_address', null, ['class' => 'form-control', 'placeholder' => 'Employer Address']) !!}
                            </div>
                        </div>
 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_city', 'City') !!}
                                {!! Form::text('employment_current_employer_city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_state', 'State') !!}
                                {!! Form::select('employment_current_employer_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_zip', 'Zip Code') !!}
                                {!! Form::text('employment_current_employer_zip', null, ['class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_job_title', 'Job Title') !!}
                                {!! Form::text('employment_current_job_title', null, ['class' => 'form-control', 'placeholder' => 'Job Title']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_hire_date', 'Hire Date') !!}
                                {{-- Form::text('employment_current_hire_date', null, ['class' => 'form-control', 'placeholder' => 'Hire Date']) --}}
                                <input type="date" class="form-control date-selection" name="employment_current_hire_date" id="employment_current_hire_date" placeholder="'MM/DD/YYYY'">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <p class="lead text-center">
                        Past Employment Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_name', 'Employer Name') !!}
                                {!! Form::text('employment_past_employer_name', null, ['class' => 'form-control', 'placeholder' => 'Employer Name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_phone', 'Employer Phone') !!}
                                {!! Form::text('employment_past_employer_phone', null, ['class' => 'form-control', 'placeholder' => 'Employer Phone']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_address', 'Employer Address') !!}
                                {!! Form::text('employment_past_employer_address', null, ['class' => 'form-control', 'placeholder' => 'Employer Address']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_city', 'City') !!}
                                {!! Form::text('employment_past_employer_city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_state', 'State') !!}
                                {!! Form::select('employment_past_employer_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_zip', 'Zip Code') !!}
                                {!! Form::text('employment_past_employer_zip', null, ['class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_job_title', 'Job Title') !!}
                                {!! Form::text('employment_past_job_title', null, ['class' => 'form-control', 'placeholder' => 'Job Title']) !!}
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_hire_date', 'Hire Date') !!}
                                {{--  Form::text('employment_past_hire_date', null, ['class' => 'form-control', 'placeholder' => 'Hire Date']) --}}
                                <input type="date" class="form-control date-selection" name="employment_past_hire_date" id="employment_past_hire_date" placeholder="'MM/DD/YYYY'">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_end_date', 'End Date') !!}
                                {{-- Form::text('employment_past_end_date', null, ['class' => 'form-control', 'placeholder' => 'End Date']) --}}
                                <input type="date" class="form-control date-selection" name="employment_past_end_date" id="employment_past_end_date" placeholder="'MM/DD/YYYY'">
                            </div>
                        </div>
                    </div>

            
                </div>
            </div>
        @endif


        @if (in_array(9, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(9)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(9)->icon }}" aria-hidden="true"></i>
                    </h1>
                    
            
                    <p class="lead text-center">
                        College/University Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_name', 'College/University Name') !!}
                                {!! Form::text('education_college_name', null, ['class' => 'form-control', 'placeholder' => 'College Name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_phone', 'Phone') !!}
                                {!! Form::text('education_college_phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_address', 'Address') !!}
                                {!! Form::text('education_college_address', null, ['class' => 'form-control', 'placeholder' => 'Address']) !!}
                            </div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_city', 'City') !!}
                                {!! Form::text('education_college_city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_state', 'State') !!}
                                {!! Form::select('education_college_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_zip', 'Zip Code') !!}
                                {!! Form::text('education_college_zip', null, ['class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                    	
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_years_attended', 'Years Attended') !!}
                                {!! Form::text('education_college_years_attended', null, ['class' => 'form-control', 'placeholder' => 'Years Attended']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_is_graduated', 'Graduated') !!}
                                {!! Form::select('education_college_is_graduated', ['1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control', 'placeholder' => '- Select -']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_graduated_year', 'Year Graduated') !!}
                                {!! Form::text('education_college_graduated_year', null, ['class' => 'form-control', 'placeholder' => 'Year Graduated']) !!}
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row">
                    	<div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_degree_type', 'Type of Degree') !!}
                                {!! Form::text('education_college_degree_type', null, ['class' => 'form-control', 'placeholder' => 'Type of Degree']) !!}
                            </div>
                        </div>
                    </div>
                    
                    

                    <hr>

                    <p class="lead text-center">
                        High School Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_name', 'High School Name') !!}
                                {!! Form::text('education_high_school_name', null, ['class' => 'form-control', 'placeholder' => 'High School Name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_city_and_state', 'Address') !!}
                                {!! Form::text('education_high_school_city_and_state', null, ['class' => 'form-control', 'placeholder' => 'Address']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_phone', 'High School Phone') !!}
                                {!! Form::text('education_high_school_phone', null, ['class' => 'form-control', 'placeholder' => 'High School Phone']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                    	<div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_city', 'City') !!}
                                {!! Form::text('education_high_school_city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_state', 'State') !!}
                                {!! Form::select('education_high_school_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_zip', 'Zip Code') !!}
                                {!! Form::text('education_chigh_school_zip', null, ['class' => 'form-control', 'placeholder' => 'Zip Code']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_years_attended', 'Years Attended') !!}
                                {!! Form::text('education_high_school_years_attended', null, ['class' => 'form-control', 'placeholder' => 'Years Attended']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_is_graduated', 'Graduated') !!}
                                {!! Form::select('education_high_school_is_graduated', ['1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control', 'placeholder' => '- Select -']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_high_school_graduation_year', 'Year Graduated') !!}
                                {!! Form::text('education_high_school_graduation_year', null, ['class' => 'form-control', 'placeholder' => 'Year Graduated']) !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
	                	<div class="col-md-4">
	                        <div class="form-group">
	                            {!! Form::label('education_high_school_degree_type', 'Type of Degree') !!}
	                            {!! Form::text('education_high_school_degree_type', null, ['class' => 'form-control', 'placeholder' => 'Type of Degree']) !!}
	                        </div>
	                    </div>
	                </div>
                </div>
            </div>
        @endif


        @if (in_array(10, $request->types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(10)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(10)->icon }}" aria-hidden="true"></i>
                    </h1>
                    

                    <p class="lead text-center">
                        Driver's License Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('license_number', 'License Number') !!}
                                {!! Form::text('license_number', null, ['class' => 'form-control', 'placeholder' => 'License Number']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('license_state_id', 'License State') !!}
                                {!! Form::select('license_state_id', cache('states')->pluck('title', 'id'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true']) !!}
                            </div>
                        </div>
                    </div>
            
                </div>
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                
                <p class="lead text-center">
                    Confirmation
                </p>
                <p class="text-center">
                    Pressing the submit button below will run the following checks:
                </p>
                
                <div class="text-center">
                @foreach ($request->types as $type_id)
                
                    <p class="text-center">
                        <span class="text-{{ cache('types')->find($type_id)->color }}">
                            <i class="fa {{ cache('types')->find($type_id)->icon }}" aria-hidden="true"></i>
                        </span>
                        {{ cache('types')->find($type_id)->title }}
                    </p>
                
                @endforeach
                </div>


                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                    	
                    	<ul>
                    		@if($isFederal)
		            	  		<li class="text-danger">Federal checks often require a minimum of 24 hours to complete. You will receive an email when the check is ready.</li>
		            	  	@endif
		            	  	
		            	  	<li class="text-danger">Click the submit button <u><b>once only</b></u>, or you may be charged for multiple checks.</li>
                    	</ul>
                        
                        
                        <!-- button type="button" class="btn btn-primary btn-lg btn-block" onclick="submitForm(this);">Submit</button -->
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="formSubmit">Submit</button>
                        
                    </div>
                </div>

                <br>

                
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}


@if (in_array(1, $request->types) && count($request->types) < 2)

    <div class="row">
	    <div class="col-md-8 col-md-offset-2">
	        <div class="panel panel-default">
	            <div class="panel-body align-items-center">
	            	<h5 class="text-center">
	            		<span class="text-{{ cache('types')->find($type_id)->color }}">
                            <i class="fa {{ cache('types')->find($type_id)->icon }}" aria-hidden="true"></i>
                        </span>
	            		National Tri-Eye Bulk Uploader
	            	</h5>
	            	<br>
	            	{!! Form::open(['url' => secure_url('import'), 'files'=>'true']) !!}
	            	<div class="align-items-center">
	            		<div class="col-md-4" style="margin: 0 auto; float: unset;">
	            			<input type="hidden" name="type" value="1">
	            			<div class="mock-uploader">
	            				<button type="button" class="btn btn-success btn-lg btn-block browse">Browse</button>
	            			    <span id="import-mirror-text" class="bg-info" style="font-size: 16px; margin-top: 6px; display: block;" >No File Selected</span>
	            			</div>
	            			<br>
		            		<input type="file" id="import" name="import" accept=".csv" style="visibility:hidden; height: 0;">
		            		<button type="submit" class="btn btn-primary btn-lg btn-block">Upload</button>
		            	</div>
	            	</div>
	            	{!! Form::close() !!}
	            	<hr>
	            	<div class="align-items-center" style="text-align: center;">
				    	<button class="btn btn-info showBulkUploader" style="width: 322px;">Click here for Bulk Loader Instructions</button>
				    </div>
	            	
	            </div>
	        </div>
	    </div>
	</div>
	
    @include('layouts.footer')
	
	<template style="display:none" id="bulk-upload-template">
		
		<div class="bulk-upload-wrapper">
			<ol>
				<li>
					Download the Bulk Upload file from here: <a href="{{ secure_url('files/National_Tri_Eye_Bulk.csv') }}" download style="color: red;">CLICK HERE</a>
				</li>
				<li>
					Fill in the information.
					<ul>
						<li>
							Middle name is optional.
						</li>
						<li>
							SS Numbers must be in the format <span style="color: red;">111-11-1111</span>.
                        </li>
                        <li>
							Birthdates must be in the format <span style="color: red;">mm/dd/yyy</span>
						</li>
						<li>
							<img src="{{ secure_url('images/bu_instr_cvs.png') }}">
						</li>
					</ul>
				</li>
				<li>
					Save the file to your system.
				</li>
				<li>
					Select the Browse option, and select the file you just saved.
				</li>
				<li>
					Click on Upload.
				</li>
			</ol>
		</div>
		
	</template>

@endif

@endsection

@section('js')
<script>

$(function() {

    $("#county_tri_eye_state").change(function(e) {
        $.get( "{{ secure_url('api/counties') }}?state_code="+$(this).val(), function( response ) {
            console.log(response);
            $('#county_tri_eye_county_ids').empty();

            $.each(response.data, function(key, county) {
                $('#county_tri_eye_county_ids')
                    .append('<option value="'+county.id+'">'+county.title_with_extra_cost+'</option>');
            });
            

            $('#county_tri_eye_county_ids').selectpicker('refresh');
        });
    });
    
    $(".mock-uploader button.browse").click(function(){
    	$("#import").trigger("click");
    })
    
    $("#import").change(function(){

    	var name = $("#import").val().split('\\').pop();
    	
    	if(name.length > 0){
    		$(".bg-info").html(name);
    	}else{
    		$(".bg-info").html("No Files Selected");
    	}

    });
    
    $(".showBulkUploader").click(function(){

    	$(".modal-wrapper").html( $("#bulk-upload-template").html() );
    	
    	    $( ".modal-wrapper" ).dialog({
			      modal: true,
			      title: 'Bulk Upload Instructions',
			      width: 'auto',
			      height: 'auto',
			      buttons: {
			        Ok: function() {
			          $( this ).dialog( "close" );
			          $(".modal-wrapper").html("");
			        }
			      }
    		});
    	
    });
    
    $("#formSubmit").click(function(e){
    	
    	$(this).prop('disabled', true);
    	e.preventDefault();
    	
	    if ($("#input_form").data('submitted') === true) {
	      // Previously submitted - don't submit again
	      //swallow
	    } else {
	      // Mark it so that the next submit can be ignored
	      $("#input_form").data('submitted', true);
	      $("#input_form").submit();
	    }
    	
    });
    
    $(".profile_select select").change(function(){
    	
    	var id = $(".profile_select option:selected").val();
    	
    	if(typeof id != null){
    		Admin.getProfile(id);
    	}
    	
    });
  

});
</script>
@endsection