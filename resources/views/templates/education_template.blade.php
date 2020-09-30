<?php
  $r = json_decode(decrypt($report->report));
  
  $testing = false;
  //$testing = true;
  
  if($testing){
  	Log::info("Display the report");
  	$s = print_r($r, true);
	Log::info($s);
  }
      
?>
<div class="row report_template">
	<div class="col-md-12">
		<div class="panel panel-default">
			
	 		@if(isset($profile->college_name))
				<div class="col-md-12">
					<h4>College</h4>
					{{-- College table goes here --}}
					<table class="table college_table">
    					
    					<tr>
    						<th class="col-md-3">Name</th>
    						<td>{{ $profile->college_name }}</td>
    					</tr>

    					@if(isset($profile->college_city_and_state))
    					<tr>
    						<th>Address</th>
    						<td>{{ $profile->college_city_and_state }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_city))
    					<tr>
    						<th>City</th>
    						<td>{{ $profile->college_city }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_state))
    					<tr>
    						<th>State</th>
    						<td>{{ $profile->college_state }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_zip))
    					<tr>
    						<th>Zip</th>
    						<td>{{ $profile->college_zip }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_phone))
    					<tr>
    						<th>Phone</th>
    						<td>{{ $profile->college_phone }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_years_attended))
    					<tr>
    						<th>Years Attended</th>
    						<td>{{ $profile->college_years_attended }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_is_graduated))
    					<tr>
    						<th>Graduated</th>
    						<td>{{ $profile->college_is_graduated ? 'Yes' : 'No' }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_graduation_year))
    					<tr>
    						<th>Graduated</th>
    						<td>{{ $profile->college_graduation_year }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->college_degree_type))
    					<tr>
    						<th>Type of Degree</th>
    						<td>{{ $profile->college_degree_type }}</td>
    					</tr>
    					@endif
    					
    				</table>
			
				</div>
	      	@endif
	          
	          
	      	@if(isset($profile->high_school_name))
				<div class="col-md-12">
					<h4>High School</h4>
					{{-- High school table --}}
					
					<table class="table high_school_table">
    					<tr>
    						<th class="col-md-3">Name</th>
    						<td>{{ $profile->high_school_name }}</td>
    					</tr>
    					
    					@if(isset($profile->high_school_city_and_state))
    					<tr>
    						<th>Address</th>
    						<td>{{ $profile->high_school_city_and_state }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_city))
    					<tr>
    						<th>City</th>
    						<td>{{ $profile->high_school_city }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_state))
    					<tr>
    						<th>State</th>
    						<td>{{ $profile->high_school_state }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_zip))
    					<tr>
    						<th>Zip></th>
    						<td>{{ $profile->high_school_zip }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_phone))
    					<tr>
    						<th>Phone</th>
    						<td>{{ $profile->high_school_phone }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_years_attended))
    					<tr>
    						<th>Years Attended</th>
    						<td>{{ $profile->high_school_years_attended }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_is_graduated))
    					<tr>
    						<th>Graduated</th>
    						<td>{{ $profile->high_school_is_graduated ? 'Yes' : 'No' }}</td>
    					</tr>
    					@endif
    					
    					@if(isset($profile->high_school_degree_type))
    					<tr>
    						<th>Type of Degree</th>
    						<td>{{ $profile->high_school_degree_type }}</td>
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