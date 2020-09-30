@extends('layouts.auth', [
    'title' => 'Reset Password',
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
            <p class="login-box-msg">Password Reset</p>

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

            {!! Form::open(['url' => secure_url('password/reset')]) !!}
                {!! Form::hidden('token', Request::get('token')) !!}

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
                      'class' => 'form-control input-lg',
                      'placeholder' => 'Password',
                      'id' => 'password'
                      ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback" aria-hidden="true"></span>
                </div>
                  
                <div class="form-group has-feedback">
                    {!! Form::password('password_confirmation', [
                      'class' => 'form-control input-lg',
                      'placeholder' => 'Confirm Password',
                      'id' => 'password_confirmation'
                      ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback" aria-hidden="true"></span>
                </div>
                

                {!! Form::submit('Reset Password', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
                
            {!! Form::close() !!}

            <p>&nbsp;</p>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection