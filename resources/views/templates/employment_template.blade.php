<?php
  $r = json_decode($report->report);
  
  $testing = false;
  //$testing = true;
  
  if($testing){
  	$s = print_r($r, true);
	Log::info($s);
  }
  

?>

<div class="row report_template">
	<div class="col-md-12">
		<div class="panel panel-default">
			
			@if(isset($profile->current_employer_name))
				<div class="col-md-12">
    				<h4>Current Employer</h4>
    				<table class="table">
    					
    					<tr>
    						<th class="col-md-3">Name</th>
    						<td>{{ $profile->current_employer_name }}</td>
    					</tr>
    					
    					@if(isset($profile->current_employer_address))
	    					<tr>
	    						<th>Address</th>
	    						<td>{{ $profile->current_employer_address }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_employer_city))
	    					<tr>
	    						<th>City</th>
	    						<td>{{ $profile->current_employer_city }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_employer_state))
	    					<tr>
	    						<th>State</th>
	    						<td>{{ $profile->current_employer_state }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_employer_zip))
	    					<tr>
	    						<th>City</th>
	    						<td>{{ $profile->current_employer_zip }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_employer_phone))
	    					<tr>
	    						<th>Phone</th>
	    						<td>{{ $profile->current_employer_phone }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_job_title))
	    					<tr>
	    						<th>Current Title</th>
	    						<td>{{ $profile->current_job_title }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->current_hire_date))
	    					<tr>
	    						<th>Hire Date</th>
	    						<td>{{ $profile->current_hire_date }}</td>
	    					</tr>
    					@endif
    					
    				</table>
			   </div>
			@elseif(isset($profile->past_employer_name))
				<div class="col-md-12">
    				<h4>Current Employer</h4>
    				<table class="table">
    					<tr>
    						<th class="col-md-3">Name</th>
    						<td>{{ $profile->past_employer_name }}</td>
    					</tr>
    					
    					@if(isset($profile->past_employer_address))
	    					<tr>
	    						<th>Address</th>
	    						<td>{{ $profile->past_employer_address }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_employer_city))
	    					<tr>
	    						<th>City</th>
	    						<td>{{ $profile->past_employer_city }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_employer_state))
	    					<tr>
	    						<th>State</th>
	    						<td>{{ $profile->past_employer_state }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_employer_zip))
	    					<tr>
	    						<th>City</th>
	    						<td>{{ $profile->past_employer_zip }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_employer_phone))
	    					<tr>
	    						<th>Phone</th>
	    						<td>{{ $profile->past_employer_phone }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_job_title))
	    					<tr>
	    						<th>Current Title</th>
	    						<td>{{ $profile->past_job_title }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_hire_date))
	    					<tr>
	    						<th>Hire Date</th>
	    						<td>{{ $profile->past_hire_date }}</td>
	    					</tr>
    					@endif
    					
    					@if(isset($profile->past_end_date))
	    					<tr>
	    						<th>Hire Date</th>
	    						<td>{{ $profile->past_end_date }}</td>
	    					</tr>
    					@endif
    				</table>
    			</div>
			@endif
			
			<div class="col-md-12">
				<h4>Validation Notes</h4>
				{{-- Validation table goes here --}}
	        	
	        	<table class="table validation_notes">
            		
            		
            		
	            	@if(isset($r->insufficientData))
						
						<tr>
							<th>Awaiting Validation</th>
						</tr>
						
					@elseif(isset($r->resultComplete))
						<tr>
							<th>PLACEHOLDER: Result is complete</th>
						</tr>
		    		@else
		    		
		    			<tr>
							<th>Awaiting Validation</th>
						</tr>
		    		
	    			@endif
            		
            	</table>
			
			</div>
		</div>
	</div>
</div>