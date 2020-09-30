<?php

  $types = \App\Models\Type::all();

?>

<div class="col-md-4">
		<div class="panel panel-default">
			
			{!! Form::open(['url' => secure_url('company/employee/screen'), 'onSubmit'=>'validateInputs();', 'id' => 'btob', 'autocomplete'=>'off']) !!}
		    	
		    	    <div class="form-group">
                        <h5>1. Enter Employee Email</h5>
                        {!! Form::text('email', "", ['class' => 'form-control', 'placeholder' => 'Email', 'type' => 'email', 'required'=>'required', 'id'=>'e_email']) !!}
                    </div>
                    
                    <div class="form-group">
	  					<h5>2. Select Check Type(s)</h5>

	  					@foreach($types as $type)
	  					
	  					  @if($type->id < 11)
	  					  
	  					    @php
	  					      $onclick = "";
	  					      
	  					      if(in_array($type->id, [3,4,6,7])){
	  					      	$onclick = 'onClick=toggleSelect(this);';
	  					      }
	  					      
	  					    @endphp
	  					  
	  					  
	  					    {{-- Hide Employment and Education checks --}}
	  					    @if(!in_array($type->id, [8,9]))
		  					<div class="row">
	  						  <strong class="col-md-8 text-right">{{ $type->title }}</strong><input {{ $onclick }} type="checkbox" class="col-md-1 check_types" name="check_types[]" value="{{ $type->id }}">
	  						</div>
	  						@endif
	  						
	  						@if($type->id == 3)
	  						    <div class="form-group selection-display">
		                          {!! Form::select('state_tri_eye_state_ids[]', cache('states')->pluck('title_with_extra_cost', 'id'), null, ['class' => 'form-control state_tri_eye_state_ids', 'multiple', 'data-live-search' => 'true']) !!}
		                        </div>
	  						@endif
	  						
	  						@if($type->id == 4)
	  						    <div class="form-group selection-display">
                        			{!! Form::select('county_tri_eye_state', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'county_tri_eye_state']) !!}
			                    </div>
			
			                    <div class="form-group county-selection-display">
			                        {!! Form::select('county_tri_eye_county_ids[]', [], null, ['class' => 'form-control', 'multiple', 'data-live-search' => 'true', 'id' => 'county_tri_eye_county_ids']) !!}
			                    </div>
	  						@endif
	  						
	  						@if($type->id == 6)
	  						    <div class="form-group selection-display">
		                          {!! Form::select('federal_state_tri_eye_state_ids[]', cache('states')->pluck('title_with_extra_cost', 'id'), null, ['class' => 'form-control federal_state_tri_eye_state_ids', 'multiple', 'data-live-search' => 'true']) !!}
		                        </div>
	  						@endif
	  						
	  						@if($type->id == 7)
	  						    <div class="form-group selection-display">
			                        {!! Form::select('federal_district_tri_eye_district_ids[]', cache('districts')->pluck('state_code_with_title', 'id'), null, ['class' => 'form-control federal_district_tri_eye_district_ids', 'multiple', 'data-live-search' => 'true']) !!}
			                    </div>
	  						@endif
	  						
	  						@if(!in_array($type->id, [8,9]))
	  						<hr>
	  						@endif
	  						
	  					  @endif
	  					  
	  					  
	  					  
  						@endforeach	  					
	  				</div>
                    
                    <div class="col-md-6 col-md-offset-3">
                        {!! Form::submit('Invite', ['class' => 'btn btn-primary btn-lg btn-block btn-success']) !!}
                    </div>
		    	{!! Form::close() !!}
			
			
			
			
		</div>
</div>


<script>
	function toggleSelect(self){

    	if( $(self).is(":checked") && $(self).parent().next().css("display") == 'none'){
    		$(self).parent().next().show(250);
    	}else{

    		$(self).parent().next().hide(250);
    		
    		if( $(self).val() == 3){
    			$(".state_tri_eye_state_ids").selectpicker('val', '');
    		}
    		
    		if( $(self).val() == 4 ){
    			$(".county-selection-display").hide();
    			$('#county_tri_eye_county_ids').selectpicker('val', '');
    			$("#county_tri_eye_state").selectpicker('val', '');
    		}
    		
    		if( $(self).val() == 6){
    			$(".federal_state_tri_eye_state_ids").selectpicker('val', '');
    		}
    		
    		if( $(self).val() == 7){
    			$(".federal_district_tri_eye_district_ids").selectpicker('val', '');
    		}
    		
    	}
    	
    }
    
    function validateInputs(evt){
    	
    	var errors = [],
    	    hasTypes = false;
    	
    	if( $("#e_email").val().length == 0){
    		errors.push("Please enter a valid email address.");
    	}

    	$(".check_types").each(function(){
	
    		if($(this).is(":checked")){
    			hasTypes = true;
    		}
    	});

    	if( hasTypes == false ){
    		errors.push("Must select at least one check type.");
    	}
    	
    	if( $(".check_types[value='3']").is(":checked") && 
    	    $(".state_tri_eye_state_ids").selectpicker('val') == '' ){
	
    		errors.push("Must select at least one state for State Tri-Eye checks.\n");
    	}
    	
    	if( $(".check_types[value='4']").is(":checked") && 
    	    $(".county_tri_eye_state").selectpicker('val') == ''){
    	    	
    		errors.push("Must select a state for County Tri-Eye checks.\n");
    	}
    	
    	if( $(".check_types[value='4']").is(":checked") && 
    	    $(".county_tri_eye_county_ids").selectpicker('val') == ''){
    	    	
    		errors.push("Must select at least one county for County Tri-Eye checks.\n");
    	}
    	
    	if( $(".check_types[value='6']").is(":checked") && 
    	    $(".federal_state_tri_eye_state_ids").selectpicker('val') == ''){
    	    	
    		errors.push("Must select at least one state for Federal State Tri-Eye checks.\n");
    	}
    	
    	if( $(".check_types[value='7']").is(":checked") && 
    	    $(".federal_district_tri_eye_district_ids").selectpicker('val') == ''){
    	    	
    		errors.push("Must select at least one district for Federal District Tri-Eye checks.\n");
    	}
    	
    	if(errors.length > 0){
    		evt.preventDefault();
    		return false;
    	}else{
    		return true;
    	}
	
    }
    
    $(function() {
	
	
		$("#county_tri_eye_state").change(function(e) {
	        $.get( "{{ secure_url('api/counties') }}?state_code="+$(this).val(), function( response ) {
	            console.log(response);
	            $('#county_tri_eye_county_ids').empty();
	
	            $.each(response.data, function(key, county) {
	                $('#county_tri_eye_county_ids')
	                    .append('<option value="'+county.id+'">'+county.title_with_extra_cost+'</option>');
	            });
	            
	
	            $('#county_tri_eye_county_ids').selectpicker('refresh');
	            $(".county-selection-display").show();
	        });
	    });

	});
    
</script>
