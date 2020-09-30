@extends('layouts.app', [
	'title' => 'Check Management',
	'active_menu_item' => 'admin_checks'
])

@section('css')

@endsection

@section('content')

@php
              
  //$companies = DB::table("users")->distinct('company')->orderBy('company')->get();
  
  $companies = DB::table("users")
		                 ->distinct('company_name')
		                 ->where('company_rep', true)
		                 ->orderBy('company_name', 'ASC')->get();
  
  $months = []; 
  
  for($m=1; $m<=12; ++$m){
    $months[] = date('F', mktime(0, 0, 0, $m, 1));
  }
  
  $maxMin = \App\Models\Company::yearRange();
  
  $max = intval(substr($maxMin["max"], 0, 4));
  $min = intval(substr($maxMin["min"], 0, 4));
  
  $types = Cache::get('types');
  
@endphp

{!! Form::open([ 'url' => secure_url('admin/checks'), 'method' => 'get'  ]) !!}
	<div class="panel panel-default">
  		<div class="panel-body">
  			<div class="row">
  				<div class="col-md-3">
  					<label for="company">Company</label>
  					<select class="form-control" name="company[]" id="company" multiple>
  					    <option value="all">All</option>
  						@foreach($companies as $company)
  						
  						    @if($company->company_name)
							<option value="{{$company->company_id}}">{{ $company->company_name }}</option>
							@endif
  						
						@endforeach
  					</select>
  				</div>

				<div class="col-md-2">
  					<label for="after">On or After</label>
  					<input type="date" class="form-control date-selection" name="after" id="after">
  				</div>
  				
  				<div class="col-md-2">
  					<label for="before">Before</label>
  					<input type="date" class="form-control date-selection" name="before" id="before">
  				</div>
  				
  				<div class="col-md-2">
  					<label for="type">Type</label>
  					<select class="form-control" name="type[]" id="type" multiple>
  						<option value="all">All</option>
  						@foreach($types as $type)
  						  <option value="{{ $type->id }}">{{ $type->title }}</option>
  						@endforeach
  					</select>
  				</div>
  				
  				<input type="hidden" name="search" id="search" value="true">
  				
  				<div class="col-md-2">
  					{!! Form::submit('Search', ['class' => 'btn btn-primary btn-lg btn-block admin-check-search']) !!}
  				</div>
  			</div>
  			
  			
  			
  		</div>
  		
  </div>
{!! Form::close() !!}

<div class="list-group">
	
	@if($checks)
	    @foreach($checks as $check)
	    
	    
	    
	    @php
	    
	      $profile = new \stdClass();
	      
	      if(null !== $check->profile){
			$profile = json_decode(Crypt::decrypt($check->profile->profile));
		  }else{
			//Log::info("Profile does not exist for this check");
		  }
		  
		  
	      
	    @endphp
	    
	      <a href="{{ secure_url('admin/checks/'.$check->id) }}" class="list-group-item">
	      	
	        @if ($check->completed_at)
	          <span class="label label-success pull-right">Completed</span>
	        @elseif($check->transaction_id =='11111111')
	          <span class="label label-primary pull-right">Removed</span>
	        @elseif($check->transaction_id =='000000')
	          <span class="label label-danger pull-right">Cancelled</span>
	        @else
	          <span class="label label-default pull-right">Pending</span>
	        @endif
	        
	        {{ $check->user->company_name }}<br>
	        
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
	        	
	        	@php	
	        		//convert UTC to EDT
	        		
	        		$date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $check->created_at, 'UTC');
					//$date->setTimezone('America/New_York');
						
		        @endphp
	        	
	          <br>Date: {{ $date->format('m-d-Y h:i a') }}
	        </p>
	        <p class="list-group-item-text text-muted">  
	          Check ID: {{ $check->id }}
	        </p>
	        
	        <p class="list-group-item-text text-muted">  
	          By: {{ $check->user->full_name }}
	          <br>By Id: {{ $check->user->id }}
	        </p>
	         
	        @if(!$check->active)
	          <p class="list-group-item-text text-muted">
	          	Deleted: {{ displayDateTime($check->updated_at) }}
	          </p>
	        @endif 
	          
	        @php
	            //$checktypes = [];
	        	$checktypes = $check->checktypes;
	        	
	        	
	        @endphp
	        
	        <p class="list-group-item-text text-muted">
	        	
		        	@foreach($checktypes as $ct)
		        	
		        		@php
			        		try{
			        			echo "Price: " . displayMoney($ct->price(),2);
			        			
			        			if( $ct->type == 10 && isset($ct->check->states[0]->code) ){
			        	  	      echo "(" . $ct->check->states[0]->code . ")";
			        	  	    }
			        			
			        		}catch(\Exception $e){
			        			//Log::info("Unable to show price for check type " . $ct->id);
			        			//Log::info($e);
			        		}

			        	@endphp
		        	@endforeach
	        </p>
	      </a>
	   @endforeach
    @else
      <a href="#" class="list-group-item">
        No recent checks
      </a>
    @endforelse
</div>

@if($checks)
<div class="text-center">
  {{ $checks->appends(request()->except('page'))->withPath('/admin/checks')->links() }}
</div>
@endif


@endsection

