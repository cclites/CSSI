@extends('layouts.app', [
	'title' => 'Employment Results',
    'active_menu_item' => 'history',
])


@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        {!! Form::open(['url' => 'admin/checks/'.$check->id.'/employment']) !!}
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1 class="text-large text-purple text-center">
                        <i class="fa fa-briefcase" aria-hidden="true"></i>
                    </h1>
                    <p class="lead text-center">
                        Employment History Results
                    </p>

                    <div class="form-group">
                        {!! Form::textarea('content', $check->employment->content, ['class' => 'form-control', 'placeholder' => 'Results']) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            {!! Form::submit('Save Employment History Results', ['class' => 'btn btn-primary btn-lg btn-block']) !!}
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

</div>
@endsection