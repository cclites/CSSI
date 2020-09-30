@extends('layouts.app', [
	'title' => 'My Account Information',
    'active_menu_item' => 'settings',
])


@section('content')

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large text-primary">
                    <i class="fa fa-address-card-o" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Contact Information
                </p>
                <p class="text-muted">
                    {{ auth()->user()->full_name }}
                    @if (auth()->user()->company_name)
                        <br>{{ auth()->user()->company_name }}
                    @endif
                    <br>{{ auth()->user()->email }}
                    <br>{{ displayPhone(auth()->user()->phone) }}
                </p>
                <p class="text-muted">
                    {{ auth()->user()->address }}
                    @if (auth()->user()->secondary_address)
                        <br>{{ auth()->user()->secondary_address }}
                    @endif
                    <br>{{ auth()->user()->city_state_zip }}
                </p>
                <p>
                    <a href="{{ secure_url('settings/contact') }}" class="btn btn-lg btn-primary">Update Contact Information</a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large text-danger">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Change Password
                </p>
                <p class="text-muted">
                    Changing your password regularly keeps your account more secure.
                </p>
                <p>
                    <a href="{{ secure_url('settings/password') }}" class="btn btn-lg btn-danger">Change Password</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection