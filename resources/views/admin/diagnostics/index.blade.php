@extends('layouts.app', [
    'title' => 'Diagnostics',
    'active_menu_parent' => 'admin',
    'active_menu_item' => 'admin_diagnostics'
])

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <h3>Send an Email</h3>
                {!! Form::open(array('url' => 'admin/diagnostics/email', 'method' => 'post')) !!}
                    <div class="form-group">
                        {!! Form::label('email', 'Email Address') !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                    </div>

                    {!! Form::submit('Send Email', ['class' => 'btn btn-primary btn-lg']) !!}
                {!! Form::close() !!}

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <h3>Exception Testing</h3>
                <p>Throw and exception to make sure it's logging/notifying correctly.</p>
                <p>
                    <a href="{{ secure_url('admin/diagnostics/exception') }}" class="btn btn-primary">Throw Exception</a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <h3>Send a Text Message</h3>
                {!! Form::open(array('url' => 'admin/diagnostics/text', 'method' => 'post')) !!}
                    <div class="form-group">
                        {!! Form::label('phone', 'Phone Number') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
                    </div>

                    {!! Form::submit('Send Text Message', ['class' => 'btn btn-primary btn-lg']) !!}
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

@endsection