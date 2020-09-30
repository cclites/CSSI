@extends('layouts.app', [
	'title' => 'Updated Terms of Use'
])

@section('content')

<h2 class="text-info terms">Please Agree to the Updated Terms of Use</h2>

@include("settings/tou");

@endSection