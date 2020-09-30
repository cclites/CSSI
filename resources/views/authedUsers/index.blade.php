@extends('layouts.app', [
	'title' => 'Authorized Users',
	'active_menu_item' => 'users_company'
])


@section('content')

<div class="list-group" id="company_users">
	<div class="col-md-6">
		
		@if(empty($users))
		
		  <h4>There are no authorized users for this company</h4>
		
		@else
		
		
		
		  @foreach($users as $user)
		  
		  	<div class="row">
		  		<div class="panel panel-danger">
					<div class="panel-body">
				  		<div class="col-md-4 col-offset-md-1">
				  			{{ $user->first_name . " " . $user->last_name }}
				  			<br>
				  			{{ $user->id }}
				  		</div>
		  		
				  		@if(Auth::user()->id == $user->id)
					  		<div class="col-md-4">
					  			Owner
					  		</div>
				  		
				  		@else
					  		<div class="col-md-5">
					  			Last Active: {{ $user->updated_at }}
					  		</div>
				  		
				  			<div class="col-md-2">
						  		@if($user->is_approved)
						  		
						  		<a class="btn btn-danger" href="{{ secure_url('company/users/' . $user->id . '/disapprove') }}">
						  			Disapprove
						  		</a>
						  		@else
						  		<a class="btn btn-warning" href="{{ secure_url('company/users/' . $user->id . '/approve') }}">
						  			Approve
						  		</a>
						  		@endif
						  	</div>
				  		
				  		@endif
		  			</div>
		  		</div>
		  	</div>
		  
		  @endforeach
		
		@endif
		
	</div>
</div>

@endsection