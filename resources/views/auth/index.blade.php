@extends('layouts.auth', [
    'title' => 'Login',
])

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ secure_url('/') }}">
                @if ($whitelabel)
                
                    {{-- 
                    <img src="{{ secure_url($whitelabel->path.'/images/logos/login.png') }}" class="logo-auth img-responsive center-block" alt="logo">
                    --}}
                @else
                    <i class="fa fa-3x fa-id-card-o" aria-hidden="true"></i>
                    <br>
                    <b>{{ env('APP_NAME') }}
                @endif
            </a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            @include('flash::message')
            
            <p class="login-box-msg">Your API Token</p>
            
            <p>
                {{ JWTAuth::fromUser(Auth::user()) }}
            </p>
            
            <p>&nbsp;</p>

            <a href="{{ secure_url('logout') }}">Logout</a><br>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@endsection