@extends('layouts.app', [
    'title' => 'Tools',
    'active_menu_parent' => 'admin',
    'active_menu_item' => 'admin_tools'
])

@section('content')

<div class="panel panel-default">
    <div class="panel-body text-center">
    	<h1 class="text-large text-primary">
            <i class="fa fa-server" aria-hidden="true"></i>
        </h1>
        <p class="lead">
            Request
        </p>
        <p>
        	<?php d($request->headers->all()); ?>
        </p>
    </div>
</div>

	    

@endsection