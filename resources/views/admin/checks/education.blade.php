@extends('layouts.app', [
	'title' => 'Education Results',
    'active_menu_item' => 'history',
])


@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        {!! Form::open(['url' => 'admin/checks/'.$check->id.'/education']) !!}
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-teal text-center">
                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        Education Verification Results
                    </p>

                    <div class="form-group">
                        {!! Form::textarea('content', $check->education->content, ['class' => 'form-control', 'placeholder' => 'Results']) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            {!! Form::submit('Save Education Verification Results', ['class' => 'btn btn-primary btn-lg btn-block']) !!}
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

</div>
@endsection