@extends('layouts.whitelabel', [
	'title' => 'Background Check for ' . $company_name,
])

@section('content')

{!! Form::open(['url' => secure_url('api/cssi/btob?token=' . $token . '&key=' . $key . '&id=' . $id)]) !!}

@php

  $params = json_decode($params); 

  foreach ($params->check_types as &$a) {
    $a = intval($a);
  }


@endphp


{!! Form::hidden('types', json_encode($params->check_types)) !!}


@foreach ($params->check_types as $type)
    {!! Form::hidden('check_types[]', (int)$type) !!}    
@endforeach


@php
  Log::info("Added Class types");
@endphp

@if(isset($params->state_tri_eye_state_ids))
	@foreach($params->state_tri_eye_state_ids as $state)
	   {!! Form::hidden('state_tri_eye_state_ids[]', $state) !!}
	@endforeach
@endif

@if(isset($params->county_tri_eye_county_ids))
	@foreach($params->county_tri_eye_county_ids as $state)
	   {!! Form::hidden('county_tri_eye_county_ids[]', $state) !!}
	@endforeach
@endif

@if(isset($params->federal_state_tri_eye_state_ids))
	@foreach($params->federal_state_tri_eye_state_ids as $state)
	   {!! Form::hidden('federal_state_tri_eye_state_ids[]', $state) !!}
	@endforeach
@endif

@if(isset($params->federal_district_tri_eye_district_ids))
	@foreach($params->federal_district_tri_eye_district_ids as $state)
	   {!! Form::hidden('federal_district_tri_eye_district_ids[]', $state) !!}
	@endforeach
@endif

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-body">
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
                            <input type="date" class="form-control date-selection" name="birthday" id="birthday" placeholder="'MM/DD/YYYY'">
                        </div>
                    </div>
                    
                    @php
                      //Log::info(gettype($params->check_types[0]));
                      //Log::info($params->check_types[0]);
                    @endphp
                    
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
                        ], $params->check_types)
                    ))
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('ssn', 'Social Security Number') !!}
                                {!! Form::text('ssn', null, ['id' => 'ssn', 'class' => 'form-control', 'placeholder' => 'XXX-XX-XXXX']) !!}
                            </div>
                        </div>
                    @endif
 
                </div>
            </div>
        </div>

        @if (in_array(8, $params->check_types))
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
                                {!! Form::label('employment_current_employer_address', 'Employer Address') !!}
                                {!! Form::text('employment_current_employer_address', null, ['class' => 'form-control', 'placeholder' => 'Employer Address']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_current_employer_phone', 'Employer Phone') !!}
                                {!! Form::text('employment_current_employer_phone', null, ['class' => 'form-control', 'placeholder' => 'Employer Phone']) !!}
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
                                {!! Form::text('employment_current_hire_date', null, ['class' => 'form-control', 'placeholder' => 'Hire Date']) !!}
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
                                {!! Form::label('employment_past_employer_address', 'Employer Address') !!}
                                {!! Form::text('employment_past_employer_address', null, ['class' => 'form-control', 'placeholder' => 'Employer Address']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('employment_past_employer_phone', 'Employer Phone') !!}
                                {!! Form::text('employment_past_employer_phone', null, ['class' => 'form-control', 'placeholder' => 'Employer Phone']) !!}
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
                                {!! Form::text('employment_past_hire_date', null, ['class' => 'form-control', 'placeholder' => 'Hire Date']) !!}
                            </div>
                        </div>
                    </div>

            
                </div>
            </div>
        @endif


        @if (in_array(9, $params->check_types))
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-{{ cache('types')->find(9)->icon }} text-center">
                        <i class="fa {{ cache('types')->find(9)->icon }}" aria-hidden="true"></i>
                    </h1>
                    
            
                    <p class="lead text-center">
                        College Information
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_name', 'College Name') !!}
                                {!! Form::text('education_college_name', null, ['class' => 'form-control', 'placeholder' => 'College Name']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_city_and_state', 'College City and State') !!}
                                {!! Form::text('education_college_city_and_state', null, ['class' => 'form-control', 'placeholder' => 'College City and State']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('education_college_phone', 'College Phone') !!}
                                {!! Form::text('education_college_phone', null, ['class' => 'form-control', 'placeholder' => 'College Phone']) !!}
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
                                {!! Form::label('education_high_school_city_and_state', 'High School City and State') !!}
                                {!! Form::text('education_high_school_city_and_state', null, ['class' => 'form-control', 'placeholder' => 'High School City and State']) !!}
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
                                {!! Form::label('education_high_school_degree_type', 'Type of Degree') !!}
                                {!! Form::text('education_high_school_degree_type', null, ['class' => 'form-control', 'placeholder' => 'Type of Degree']) !!}
                            </div>
                        </div>
                    </div>

            
                </div>
            </div>
        @endif


        @if (in_array(10, $params->check_types))
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
                
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        {!! Form::submit('Submit', ['class' => 'btn btn-primary btn-lg btn-block']) !!}
                    </div>
                </div>

                <br>

                
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}


@endsection
