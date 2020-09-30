@extends('layouts.app', [
	'title' => 'Dashboard',
	'active_menu_item' => 'reports'
])

@section('content')

@if(auth()->user()->company_rep)

<div class="row">
	<div class="col-sm-3">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-primary">
		    				@if(auth()->user()->company_rep)
		    					{{ auth()->user()->company()->company_pending_checks()->count() }}
		    				@else
		    					{{ auth()->user()->pending_checks()->count() }}
		    				@endif
		    			</span>
		    		</strong>
		    	</h1>
		    	<p class="text-muted">
		    		Pending Checks
		    	</p>
		    </div>
		</div>
	</div>

	<div class="col-sm-3">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-primary">
		    				@if(auth()->user()->company_rep)
		    					{{ auth()->user()->company()->company_this_month_checks()->count() }}
		    				@else
		    					{{ auth()->user()->this_month_checks()->count() }}
		    				@endif
		    			</span>
		    		</strong>
		    	</h1>
		    	<p class="text-muted">
		    		Checks This Month
		    	</p>
		    </div>
		</div>
	</div>
	
	<div class="col-sm-3">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-primary">
		    				@if(auth()->user()->company_rep)
		    					{{ auth()->user()->company()->company_last_month_checks()->count() }}
		    				@else
		    					{{ auth()->user()->last_month_checks()->count() }}
		    				@endif
		    			</span>
		    		</strong>
		    	</h1>
		    	<p class="text-muted">
		    		Checks Last Month
		    	</p>
		    </div>
		</div>
	</div>

    
	<div class="col-sm-3">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-primary">
		    				@if(auth()->user()->company_rep)
		    					{{ auth()->user()->company()->company_checks()->count() }}
		    				@else
		    					{{ auth()->user()->checks()->count() }}
		    				@endif
		    			</span>
		    		</strong>
		    	</h1>
		    	<p class="text-muted">
		    		Total Checks
		    	</p>
		    </div>
		</div>
	</div>
  
    
</div> <!-- /.row -->
@endif


<div class="panel panel-default">
	<div class="panel-body">
		<h4>Recent Checks</h4>
	</div>
	<div class="list-group">
	
	@php
	
		$checks = auth()->user()->company()->company_checks->take(10);
		
		/*
		$checks = App\Models\Check::hasNoCssiData()
				  ->where('company_id', auth()->user()->company()->company_id)
				  ->take(10);
				  */
		
	@endphp	
		
	    @forelse($checks as $check)
		
			<a href="{{ secure_url('checks/'.$check->id) }}" class="list-group-item">
				@if ($check->completed_at)
					<span class="label label-success pull-right">Completed</span>
				@else
					<span class="label label-default pull-right">Pending</span>
				@endif
				
				<br>
				
				<span class="indicator-row pull-right">
		        	
		        	@if($check->viewed)
		        	  <i class="fa fa-eye fa-lg text-primary" aria-hidden="true" title="Has Been Viewed"></i>
		        	@endif
		        	
		        	@if($check->has_offense)
		        	  <i class="fa fa-bell-o fa-lg text-warning" aria-hidden="true" title="Has Offenses"></i>
		        	@endif
		        	
		        	@if($check->has_sex_offense)
		        	  <i class="fa fa-exclamation-triangle fa-lg text-danger" aria-hidden="true" title="Has Sex Offense Indictor"></i>
		        	@endif
		        </span>
				
				
				<h4 class="list-group-item-heading">
				  <strong>{{ $check->full_name }}</strong>
				</h4>
				<ul class="list-unstyled">
				  @foreach ($check->types as $type)
				    <li>
				        <span class="text-{{ $type->color }}">
				            <i class="fa fa-fw {{ $type->icon }}" aria-hidden="true"></i>
				        </span>
				        {{ $type->title }}
				    </li>
				  @endforeach
				</ul>
				<p class="list-group-item-text text-muted">
					Date: {{ displayDateTime($check->created_at) }}
				</p>
				
				@if(auth()->user()->company_rep)
				   <p class="list-group-item-text text-muted">
				   	  By: {{ $check->user->first_name  . " " . $check->user->last_name }}
				   </p>
				@endif
			</a>
		@empty
			<a href="#" class="list-group-item text-center">
				No recent checks
			</a>
		@endforelse
	</div>
</div>



@endsection