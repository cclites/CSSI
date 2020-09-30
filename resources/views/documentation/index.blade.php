@extends('layouts.documentation', [
	'title' => 'Documentation Home',
    'active_menu_item' => 'documentation_home',
])


@section('content')

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large text-danger">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    API Documentation
                </p>
                <p class="text-muted">
                    API Documentation is currently being written. Please check back soon for updates.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection