@extends('layouts.app', [
	'title' => 'Checks',
	'active_menu_item' => 'checks'
])

@section('css')

@endsection

@section('content')

@php
  $types = Cache::get('types');
  
  cLog("Showing the checks", 'resources/views/checks', 'index');
  

@endphp

{!! Form::open([ 'url' => secure_url('checks'), 'method' => 'get'  ]) !!}
<div class="panel panel-default">
  <div class="panel-body">
    <div class="row">
      <div class="col-md-9">
        <div class="form-group has-feedback">
            {!! Form::text('search', $request->search ?: null, ['class' => 'form-control input-lg', 'placeholder' => 'Search']) !!}
            <i class="fa fa-search form-control-feedback" aria-hidden="true"></i>
            <input type="hidden" name="q" value="true">
        </div>      
      </div>
      <div class="col-md-3">
        {!! Form::submit('Search', ['class' => 'btn btn-primary btn-lg btn-block']) !!}
      </div>
    </div>
  </div>
</div>
{!! Form::close() !!}


<div class="list-group">
	
	 @if(count($checks) > 0)

	    @foreach($checks as $check)
	    
	      @if( ($check->active && $check->viewable) || ($check->active && Auth::user()->company_rep) )
	      
	       
		      <a href="{{ secure_url('checks/'.$check->id) }}" class="list-group-item">
		        @if ($check->completed_at)
		          <span class="label label-success pull-right">Completed</span>
		        @elseif($check->transaction_id =='11111111')
		          <span class="label label-primary pull-right">Removed</span>
		        @elseif($check->transaction_id =='000000')
		          <span class="label label-danger pull-right">Cancelled</span>
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
		        
		        @if(Auth::user()->hasRole('limited_admin'))
		          <p class="list-group-item-text text-muted">
		            {{ $check->user->company_name }}
		          </p>
		          <br>
		        @endif
		        
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
		        	
		          @php	
	        		//convert UTC to EDT
	        		
	        		$date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $check->created_at, 'UTC');
					//$date->setTimezone('America/New_York');
						
		        @endphp
		          
		          Date: {{ $date->format('m-d-Y h:i a') }}
		        </p>
		        
		        @php
		        	//$checktypes = [];
		        	$checktypes = $check->checktypes;
		        @endphp
		        
		        @if(Auth::user()->company_rep)
		        <p class="list-group-item-text text-muted">
		        	
		        @php
		        
		        
		            foreach($checktypes as $ct){
		            	
		            	//$ct->check->states[0]->code
		            	
		        	  //Price: {{ displayMoney($ct->price(),2) }}<br>
		        	  
		        	  try{
		        			//echo "Price: " . displayMoney($ct->price(),2);
		        			
		        			
		        			if( isset($ct->check->states[0]->code) ){
		        	  	      echo "(" . $ct->check->states[0]->code . ")";
		        	  	    }
		        	  	    
		        	  	   
		        	  	    echo "<br>";
		        			
		        		}catch(\Exception $e){
		        			Log::info("Unable to show price for check type " . $ct->id);
		        		}
		        	  
		        	  
		        	  
		        	}
		        
		        
		            /*
			        	
	        		*/
	        	@endphp

		        </p>
		        @endif
		        
		        <p class="list-group-item-text text-muted">
		        	Requested By: {{ $check->user->first_name . " " . $check->user->last_name . "  (" . $check->user->id . ")"}}<br>
		        </p>
		        
		      </a>
		      
		      
		      
		      
	      @endif  {{-- End of check link--}}
	      
	    @endforeach
	    
	  @else
	      <a href="#" class="list-group-item">
	        No recent checks
	      </a>
	  @endif
</div>

@if(count($checks) > 0)
	<div class="text-center">
	  {{ $checks->appends(request()->except('page'))->withPath('/checks')->links() }}
	</div>
@endif


@endsection

