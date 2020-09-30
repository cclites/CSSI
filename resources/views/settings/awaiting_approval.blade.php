@extends('layouts.app', [
	'title' => 'Awaiting Approval',
])

@section('content')
<div class="row row-settings">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-body text-center">
				<h1 class="text-large text-primary">
		    		<i class="fa fa-clock-o" aria-hidden="true"></i>
		    	</h1>
		    	<p class="lead">
		    		Awaiting Approval
		    	</p>
		    	<p class="text-muted">
		    		Your account must be approved by an administrator before you may proceed. This usually happens within a few hours, but may take as long as 72 hours.
		    	</p>
			</div>
		</div>
	</div>
</div>

	
@endsection