@extends('layouts.auth', [
    'title' => 'Sign Up',
])

@section('content')

    <style>
    	#agree_to_terms{
    		display: none;
    	}
    	
    	div.modal-dialog{
    		width: 98%;
    		height: 98%;
    	}
    	
    	div.modal-content{
    		padding-right: 20px;
    		padding-left: 12px;
    	}

    </style>
    
    <div class="login-box" style="margin: 10px auto;">
        <div class="login-logo">
            <a href="{{ secure_url('/') }}">
                @if ($whitelabel)
                    <img src="{{ secure_url($whitelabel->path.'/images/logos/login.png') }}" class="logo-auth img-responsive center-block" alt="logo">
                @else
                    <i class="fa fa-3x fa-id-card-o" aria-hidden="true"></i>
                    <br>
                    <b>{{ env('APP_NAME') }}
                @endif
            </a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign up for a new account</p>

            @include('flash::message')
            @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            {!! Form::open(['url' => secure_url('signup')]) !!}

                <div class="form-group has-feedback">
                    {!! Form::text('first_name', null, [
                        'class' => 'form-control',
                        'placeholder' => 'First Name',
                        'id' => 'first_name'
                    ]) !!}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    {!! Form::text('last_name', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Last Name',
                        'id' => 'last_name'
                    ]) !!}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    {!! Form::email('email', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Email',
                        'id' => 'email'
                    ]) !!}
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                
                <div class="form-group has-feedback">
                    {!! Form::password('password', [
                        'class' => 'form-control',
                        'placeholder' => 'Create Password',
                        'id' => 'password'
                    ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    {!! Form::password('password_confirmation', [
                        'class' => 'form-control',
                        'placeholder' => 'Password Confirmation',
                        'id' => 'password_confirmation'
                    ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                
                <div class="form-row">
                	<strong>** If you are a current EyeForSecurity.com subscriber migrating from the old system, leave the company ID blank. If your company account owner has already created an account on this system and you are an employee of a current EyeForSecurity.com subscriber, provide your 6-digit EyeForSecurity.com company code below. Please note: the company ID is case sensitive and must look exactly how it is provided on the account owner's login.</strong>
                	
                </div>
                
                <br>
                
                <div class="form-group has-feedback">
                    {!! Form::text('companyId', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Company ID (if current user)',
                        'id' => 'companyId',
                        'maxlength' => 6,
                        'title' => 'Only use this field if you already have a CSSI generated company code'
                    ]) !!}
                    <span class="glyphicon glyphicon-cog form-control-feedback"></span>
                </div>
                
                {{-- Hide all of this --}}
                <div class="row" id="agree_to_terms">
                    <div class="col-xs-8">
                        <!--div class="checkbox icheck"-->
                            <label>
                                {!! Form::checkbox('terms', 'agree') !!}
                                I agree to the <a target="_blank" href="#" data-toggle="modal" data-target="#modal-terms">Terms</a>
                            </label>
                        <!--/div-->
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat btn-signup" disabled>Sign Up</button>
                    </div>
                    <!-- /.col -->
                </div>
                
                <div class id="show_terms">
                	<a target="_blank" href="#" data-toggle="modal" data-target="#modal-terms"><button class="btn btn-lg btn-info btn-block">Continue......</button></a>
                </div>
            </form>

            <p>&nbsp;</p>

            <a href="{{ secure_url('login') }}">Already have an account?</a><br>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->



    <!-- Modal -->
    <div class="modal fade" id="modal-terms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

        	@php $isRegistration = true; @endphp
        	<br><br>
        	<h2 class="text-info">Terms of Use</h2>
        	@include("settings.tou");
          
        </div>
      </div>
    </div>
    
    
@endsection