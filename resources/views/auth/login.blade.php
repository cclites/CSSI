@extends('layouts.auth', [
    'title' => 'Login',
])

@section('content')

	<!--div style="margin: 20px auto 0; border: 1px solid #bbb; background-color: #fff; width: 800px; padding: 12px; border-radius: 6px;">
	    <p>
	    	<h3><strong>Attention Current Clients:</strong></h3>
	    	  <br>
	    	 We have launched our new client portal! If you have not already signed up for a new account, please click “Sign up for a New Account” and create an account.  
	    	 
	    	 <br><br>
	    	 
	    	 Click <a href="https://www.eyeforsecurity.com/admin/oldlogin.php">HERE</a> if you would like to go back to the old portal to see past background checks. Please note: you will only be able to use the old portal to look at old background checks and you will not be able to run any new ones. 
	    	 
	    	 <br><br>
	    	 
	    	 If you have any questions please call us at 1-800-203-4731.
	    </p>
	
	</div-->

    <div class="login-box" style="margin: 1% auto;">
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
            <p class="login-box-msg">Sign in to start your session</p>
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

            {!! Form::open(['url' => secure_url('login')]) !!}

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
                        'placeholder' => 'Password',
                        'id' => 'password'
                    ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                
                    
                <button type="submit" class="btn btn-primary btn-block btn-lg btn-flat">Sign In</button>
                    
                
            </form>

            <p>&nbsp;</p>

            <a href="{{ secure_url('password') }}">I forgot my password</a><br>
            <a href="{{ secure_url('signup') }}" class="text-center">Sign up for a new account</a>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection