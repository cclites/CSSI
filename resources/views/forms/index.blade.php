@extends('layouts.app', [
	'title' => 'Forms',
	'active_menu_item' => 'forms'
])

@section('content')

<div class="row">
	<div class="col-md-6 col-md-offset-3">

		<div class="panel panel-default">
			<div class="panel-body">
				<h1 class="text-large text-primary text-center">
		    		<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
		    	</h1>
		    	<p class="lead text-center">
		    		Forms
		    	</p>
			</div>
			<div class="list-group">
				<a href="{{ secure_url('files/consent_release_form_10012005.pdf') }}" target="_blank" class="list-group-item">
					<p class="list-group-item-text">
						<strong>Consent Release Form</strong>
					</p>
				</a>
				<a href="{{ secure_url('files/FCRASample.pdf') }}" target="_blank" class="list-group-item">
					<p class="list-group-item-text">
						<strong>Sample pre notice of adverse action form</strong>
					</p>
				</a>
				<a href="{{ secure_url('files/FCRARights.pdf') }}" target="_blank" class="list-group-item">
					<p class="list-group-item-text">
						<strong>Summary of your rights under FCRA (English)</strong>
					</p>
				</a>
				<a href="{{ secure_url('files/FCRAespanol.pdf') }}" target="_blank" class="list-group-item">
					<p class="list-group-item-text">
						<strong>Summary of your rights under FCRA (Spanish)</strong>
					</p>
				</a>
			</div>
		</div>

	</div>
</div>


@endsection