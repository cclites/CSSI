@extends('layouts.app', [
	'title' => 'Password',
    'active_menu_item' => 'settings',
])


@section('content')

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        {!! Form::open(['url' => secure_url('settings/password') ]) !!}
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-danger text-center">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        Change Password
                    </p>

                    <div class="form-group">
                        {!! Form::label('current_password', 'Current Password') !!}
                        {!! Form::password('current_password', ['class' => 'form-control', 'placeholder' => 'Current Password']) !!}
                    </div>

                    <hr>

                    <div class="form-group">
                        {!! Form::label('password', 'New Password') !!}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('password_confirmation', 'Repeat New Password') !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Repeat New Password']) !!}
                    </div>


                    <p></p>
                    {!! Form::submit('Update Password', ['class' => 'btn btn-danger btn-lg btn-block']) !!}
                    
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection