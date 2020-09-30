@extends('layouts.app')


@section('content')

<style>

	.tou_explainer{
		width: 800px;
		margin: 0 auto;
	}

	.tou_explainer p{
		font-size: 22px;
	}	
</style>

<div class="tou_explainer">
	<h2 class="text-center">Pardon the Interruption</h2>
	<p>
		Due to new compliance regulations, we have updated our Terms of Use. All users must read and agree to the Terms before continuing to use
		EyeForSecurity.com.
		<br><br>
		Call 1-800-203-4731 or email jettore@eyeforsecurity.com with any questions.
		<br><br>
		Click on 'Next' to contiue to the Terms of Service.
		<br>
		<hr>
		<a href="{{ secure_url('tou') }}"><button type="button" class="btn btn-lg btn-block btn-success">Next</button></a>		
	</p>
</div>
@endsection
