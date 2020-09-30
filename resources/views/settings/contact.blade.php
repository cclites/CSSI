@extends('layouts.app', [
	'title' => 'Contact Information',
    'active_menu_item' => 'settings',
])


@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        {!! Form::open(['url' => secure_url('settings/contact')]) !!}
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-primary text-center">
                        <i class="fa fa-address-card-o" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        Contact Information
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                        	
                        	@if(Auth::user()->company_rep)
                            <div class="form-group">
                                {!! Form::label('company_name', 'Company') !!}
                                {!! Form::text('company_name', Auth::user()->company_name, ['class' => 'form-control', 'placeholder' => 'Company']) !!}
                            </div>
                            @else
                            <div class="form-group">
                            	{!! Form::label('company', 'Company') !!}
                                {!! Form::text('company_name', Auth::user()->company_name, ['class' => 'form-control', 'placeholder' => 'Company', 'disabled'=> 'true'] ) !!}
                                <input type="hidden" name="company_name" value="{{ Auth::user()->company_name }}">
                            </div>
                            @endif
                            
                            <div class="row">
	                            <div class="col-md-8">
	                               <div class="form-group">
	                                  {!! Form::label('phone', 'Phone') !!}
	                                  {!! Form::text('phone', displayPhone(Auth::user()->phone), ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
	                                </div>
	                            </div>
	                            
	                            <div class="col-md-4">
	                            	<div class="form-group">
	                                  {!! Form::label('extension', 'Extension') !!}
	                                  {!! Form::text('extension', Auth::user()->extension, ['class' => 'form-control', 'placeholder' => 'Ext']) !!}
	                                </div>
	                            </div>
                            </div>
                            
                            <div class="form-group">
                              {!! Form::label('cell_phone', 'Cell Phone') !!}
                              {!! Form::text('cell_phone', displayPhone(Auth::user()->cell_phone), ['class' => 'form-control', 'placeholder' => 'Cell Phone']) !!}
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('email', 'Email') !!}
                                {!! Form::text('email', Auth::user()->email, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                            </div>
                            
                            {{-- 
							@if(Auth::user()->company_rep)
                            <div class="form-group">
                                {!! Form::label('companyId', 'Company ID') !!}
                                {!! Form::text('companyId', Auth::user()->company_id, ['class' => 'form-control', 'placeholder' => 'Company ID']) !!}
                            </div>
                            @else
                            --}}
                            
                            
                            <div class="form-group">
                            	{!! Form::label('companyId', 'Company ID') !!}
                                {!! Form::text('companyId', Auth::user()->company_id, ['class' => 'form-control', 'placeholder' => 'Company ID', 'disabled'=>'true']) !!}
                                <input type="hidden" name="companyId" value="{{ Auth::user()->company_id }}">
                            </div>
                            {{--  @endif --}}
							
                        </div>

                        <div class="col-md-6">
                            
                            @if(Auth::user()->company_rep)
                            <div class="form-group">
                                {!! Form::label('address', 'Address (Line 1)') !!}
                                {!! Form::text('address', Auth::user()->address, ['class' => 'form-control', 'placeholder' => 'Address (Line 1)']) !!}
                            </div>
                            @else
                            <div class="form-group">
                            	{!! Form::label('address', 'Address (Line 1)') !!}
                                {!! Form::text('address', Auth::user()->address, ['class' => 'form-control', 'placeholder' => 'Address (Line 1)', 'disabled'=>'true']) !!}
                                <input type="hidden" name="address" value="{{ Auth::user()->address }}">
                            </div>
                            @endif

							@if(Auth::user()->company_rep)
                            <div class="form-group">
                                {!! Form::label('secondary_address', 'Address (Line 2)') !!}
                                {!! Form::text('secondary_address', Auth::user()->secondary_address, ['class' => 'form-control', 'placeholder' => 'Address (Line 2)']) !!}
                            </div>
                            @else
                            <div class="form-group">
                            	{!! Form::label('secondary_address', 'Address (Line 2)') !!}
                                {!! Form::text('secondary_address', Auth::user()->secondary_address, ['class' => 'form-control', 'placeholder' => 'Address (Line 2)', 'disabled'=>'true']) !!}
                                <input type="hidden" name="secondary_address" value="{{ Auth::user()->secondary_address }}">
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-5">
                                	
                                	@if(Auth::user()->company_rep)
                                    <div class="form-group">
                                        {!! Form::label('city', 'City') !!}
                                        {!! Form::text('city', Auth::user()->city, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                                    </div>
                                    @else
                                    <div class="form-group">
		                            	{!! Form::label('city', 'City') !!}
                                        {!! Form::text('city', Auth::user()->city, ['class' => 'form-control', 'placeholder' => 'City', 'disabled'=>'true']) !!}
                                        <input type="hidden" name="city" value="{{ Auth::user()->city }}">
		                            </div>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                	
                                	@if(Auth::user()->company_rep)
                                    <div class="form-group">
                                        {!! Form::label('state', 'State') !!}
                                        {!! Form::text('state', Auth::user()->state, ['class' => 'form-control', 'placeholder' => 'State']) !!}
                                    </div>
                                    @else
                                    <div class="form-group">
		                            	{!! Form::label('state', 'State') !!}
                                        {!! Form::text('state', Auth::user()->state, ['class' => 'form-control', 'placeholder' => 'State', 'disabled'=>'true']) !!}
                                        <input type="hidden" name="state" value="{{ Auth::user()->state }}">
		                            </div>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                	@if(Auth::user()->company_rep)
                                    <div class="form-group">
                                        {!! Form::label('zip', 'Zip') !!}
                                        {!! Form::text('zip', Auth::user()->zip, ['class' => 'form-control', 'placeholder' => 'Zip']) !!}
                                    </div>
                                    @else
                                    <div class="form-group">
		                            	{!! Form::label('zip', 'Zip') !!}
                                        {!! Form::text('_zip', Auth::user()->zip, ['class' => 'form-control', 'placeholder' => 'Zip', 'disabled'=>'true']) !!}
                                        <input type="hidden" name="zip" value="{{ Auth::user()->zip }}">
		                            </div>
                                    @endif
                                    
                                </div>
                            </div>
                            
 	
                        	@if(Auth::user()->company_rep)
                        	<div class="row">
                        		<div class="col-md-4">
		                        	<div class="form-group">
		                        		{!! Form::label('authorizedRep', 'Account Owner') !!}
		                        		<br>
		                        		{!! Form::checkbox('authorizedRep', 'true', true) !!}
		                        	</div>
		                        </div>
	                        	
	                        	<div class="col-md-8">
		                        	<div class="form-group">
		                            	{!! Form::label('invoice', 'Invoicing Email') !!}
		                                {!! Form::text('invoice', Auth::user()->invoice, ['class' => 'form-control', 'placeholder' => 'Email Address']) !!}
		                            </div>
		                        </div>
                        	</div>
                            @endif

                            
                        </div>
                        
                        
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            {!! Form::submit('Save Contact Information', ['class' => 'btn btn-primary btn-lg btn-block']) !!}
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

</div>
@endsection