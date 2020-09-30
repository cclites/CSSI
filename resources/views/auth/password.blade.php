@extends('layouts.auth', [
    'title' => 'Password Reset Request',
])

@section('content')
    <div class="login-box">
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
            <p class="login-box-msg">Enter the email address associated with your account, and we’ll email you a link to reset your password.</p>

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

            {!! Form::open(['url' => secure_url('password')]) !!}

                <div class="form-group has-feedback">
                    {!! Form::email('email', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Email',
                        'id' => 'email'
                    ]) !!}
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                

                {!! Form::submit('Send Reset Link', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
                
            {!! Form::close() !!}

            <p>&nbsp;</p>

            <a href="{{ secure_url('login') }}">Login</a><br>
            <a href="{{ secure_url('signup') }}" class="text-center">Sign up for a new membership</a>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection