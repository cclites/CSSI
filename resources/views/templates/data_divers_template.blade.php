<?php

  Log::info("Display Data Diver Record");

  $r = json_decode(decrypt($report->report));

  //hack to make sure report has been deserialized
  if( gettype($r) == 'string'){
  	$r = json_decode($r);
  }
  
  //Accidentally introduced a different way of storing data-diver records, so this pulls the correct part of the data
  //needed to generate the report.
  //
  //UPDATE: 9/22/2018
  //The condition that caused the issue has been corrected. This code is being left here
  //to be compatible with the records that were stored incorrectly. Those records are
  //were created in July and August of 2018.
  if(isset($r->xml)){
  	$r = $r->xml;
	$r = json_decode($r);
  }

  $testing = false;
  //$testing = true;
  
  if($testing){
    Log::info("============================== TESTING MODE  ==========================================\n");
  	$s = print_r($r, true);
    Log::info($s);
    Log::info("=======================================================================================\n");
  }
  
  $parsedAddresses = [];
  
  //this check indicates a legacy
  if(isset($r->AddressHistoryResponse->Individual)){
  	
      if(is_array($r->AddressHistoryResponse->Individual)){
      	
		  //get the addresses from each
		  foreach ($r->AddressHistoryResponse->Individual as $individual) {
		  	
			  if(isset($individual->AddressHistory)){
	
				  if(is_array($individual->AddressHistory->Address)){
					  $parsedAddresses = array_merge($parsedAddresses, $individual->AddressHistory->Address);
				  }elseif(is_object($individual->AddressHistory->Address)){
					  $parsedAddresses[] = $individual->AddressHistory->Address;
				  }
			  }
		  }
		  
      }elseif(is_object($r->AddressHistoryResponse->Individual)){
      	  		
      	  $individual = $r->AddressHistoryResponse->Individual;

		  if(isset($individual->AddressHistory)){

			  if(is_array($individual->AddressHistory->Address)){
				  $parsedAddresses = array_merge($parsedAddresses, $individual->AddressHistory->Address);
			  }elseif(is_object($individual->AddressHistory->Address)){
				  $parsedAddresses[] = $individual->AddressHistory->Address;
			  }
			  
		  }
      }

  }else if( isset($r->AddressHistoryResponse->AddressHistory->Addresses)){

	if(isset($r->AddressHistoryResponse->AddressHistory->Addresses->address)){
		
		if(is_array($r->AddressHistoryResponse->AddressHistory->Addresses->address)){
			$parsedAddresses = $r->AddressHistoryResponse->AddressHistory->Addresses->address;
		}else if( is_object($r->AddressHistoryResponse->AddressHistory->Addresses->address) ){
			$parsedAddresses[] = $r->AddressHistoryResponse->AddressHistory->Addresses->address;
		}
	}
  }
  
  
  
  if($testing){
  	Log::info("Addresses have been parsed");
  }

  if(isset($r->SubjectDetail->Aliases->Alias) && is_array($r->SubjectDetail->Aliases->Alias) ){
  	$aliases = $r->SubjectDetail->Aliases;
  }else if(isset($r->SubjectDetail->Aliases->Alias) && !is_array($r->SubjectDetail->Aliases->Alias)){
  	$aliases[] = $r->SubjectDetail->Aliases->Alias;
  }else{
  	$aliases = null;
  }
  
  if($testing){
  	Log::info("Aliases have been parsed");
  }
  
  $isAdmin = Auth::user()->hasRole('admin') ? true : false;
  
  //testing over-ride
  //$isAdmin = false;
   
?>

    <style>
    	.admin-meta-info{
    		background-color: #eee;
    	}
    </style>

	<div class="row report_template">
		<div class="col-md-12">
			<div class="panel panel-default">
				
				
				<div class="panel-body">

					@if( isset($r->Errors) )
					  <div class="alert alert-danger">{{  $r->Errors->Error }}</div>
					  
					  @php
					    return;
					  @endphp
					  
					@endif
					
					@php
					  /*
					  Log::info("Show State Issued");
					  Log::info(gettype($r->AddressHistoryResponse->Summary->StateIssued));
					  Log::info(json_encode($r->AddressHistoryResponse->Summary->StateIssued));
					  
					  Log::info("Show year issued");
					  Log::info(gettype($r->AddressHistoryResponse->Summary->YearIssued));
					  Log::info(json_encode($r->AddressHistoryResponse->Summary->YearIssued));
					  */
					 
					 //Log::info(json_encode($r->AddressHistoryResponse->Summary));
					 
					@endphp
					
					
					
                    {{-- Only Tri-Eyes have addresses --}}
					@if(isset($r->AddressHistoryResponse))
					
						<h4>SSN Validation</h4>
						
						<div class="panel-container">
	
							<div class="col-md-4">
								<strong>Validation</strong> 
							</div>
							
							<div class="col-md-8">
								@php
								  $r->AddressHistoryResponse->Summary->StateIssued = (array)$r->AddressHistoryResponse->Summary->StateIssued;
								  $r->AddressHistoryResponse->Summary->YearIssued = (array)$r->AddressHistoryResponse->Summary->YearIssued;
								@endphp
								
								@if(isset($r->AddressHistoryResponse->Summary->StateIssued) 
								    /*&& !is_object($r->AddressHistoryResponse->Summary->StateIssued)*/
								    && count($r->AddressHistoryResponse->Summary->StateIssued) > 0)
									Issued in: {{ $r->AddressHistoryResponse->Summary->StateIssued[0] }}
								@else
									Unable to validate state of issuance
								@endif
								
							</div>
							
							
								<div class="col-md-4">
									<strong>Year Issued</strong> 
								</div>
								<div class="col-md-8">
									
									@if(isset($r->AddressHistoryResponse->Summary->YearIssued) 
									         /*&& !is_object($r->AddressHistoryResponse->Summary->YearIssued)*/
									         && count($r->AddressHistoryResponse->Summary->YearIssued) > 0)
										{{ $r->AddressHistoryResponse->Summary->YearIssued[0] }}
									@else
										Unable to validate date of issuance
									@endif
	
								</div>
							
							
						</div> {{-- CLOSE PANEL-CONTAINER --}}
					
					@endif	
					
					
					
					@php
					  if($testing){
					  	Log::info("Parsed Addresses type is " . gettype($parsedAddresses));
					  }
					@endphp
					
					{{--  PARSED ADDRESSES --}}
					@if(is_array($parsedAddresses) && count($parsedAddresses) > 0)
					
						@php
						  if($testing){
						  	Log::info("Show Address History Heading");
						  }
						@endphp
					
						<div class="panel-container">
							<h4>Address History</h4>
							
							{{-- HEADINGS --}}
							<div class="col-md-3">
								<h5 class="subheading">Address</h5>
							</div>
							
							<div class="col-md-3">
								<h5 class="subheading">City</h5>
							</div>
							
							<div class="col-md-1">
								<h5 class="subheading">State</h5>
							</div>
							
							<div class="col-md-1">
								<h5 class="subheading">Zip</h5>
							</div>
							
							<div class="col-md-2">
								<h5 class="subheading">County</h5>
							</div>
							
							<div class="col-md-1">
								<h5 class="subheading">From</h5>
							</div>
							
							<div class="col-md-1">
								<h5 class="subheading">Until</h5>
							</div>
							{{-- END HEADINGS --}}
							
							{{-- ADDRESS --}}
							
							@php
							  //Log::info(count($parsedAddresses));
							  
							  //Log::info(json_encode($parsedAddresses));
							  
							  //$parsedAddresses = removeDuplicates($parsedAddresses);
							@endphp
							
							@foreach($parsedAddresses as $address)
								
								    @php 
								    
								      if(is_object($address)){
								      	//$address = array($address);
								      }
								    
								    @endphp
								
							        @if(isset($address->StreetAddress))
							        
							            @php Log::info("address->StreetAddress is set"); @endphp
							        
							            @if(isset($address->StreetAddress))
										<div class="col-md-3">
											@if(isset($address->StreetAddress))
											  {{ $address->StreetAddress }}
											@else
											  &nbsp;
											@endif
										</div>
									    @endif
										
										<div class="col-md-3">
											@if(isset($address->City))
											  {{ $address->City }}
											@else
											  &nbsp;
											@endif
										</div>
										
										<div class="col-md-1">
											@if(isset($address->State))
											  {{ $address->State }}
											@else
											  &nbsp;
											@endif
										</div>
										
										
										<div class="col-md-1">
											@if(isset($address->Zipcode))
											  {{ $address->Zipcode }}
											@else
											  &nbsp;
											@endif
										</div>
										
										
										<div class="col-md-2">
											@if(isset($address->County))
											  {{ $address->County }}
											@else
											  &nbsp;
											@endif
										</div>
										
										<div class="col-md-1">
											@if(isset($address->StartDate->Month))
												{{ $address->StartDate->Month . "-" . $address->StartDate->Year }}
											@else
											    &nbsp;
											@endif
										</div>
										
										<div class="col-md-1">
											@if(isset($address->EndDate->Month))
												{{ $address->EndDate->Month . "-" . $address->EndDate->Year }}
											@else
											    &nbsp;
											@endif
										</div>
										
									@elseif(isset($address->fullStreet))
									
										@php //Log::info("address->fullStreet is set"); @endphp
									
										<div class="col-md-3">
											@if(isset($address->fullStreet)  && !is_object($address->fullStreet) )
											  {{ $address->fullStreet }}
											@else
											  &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed fullStreet");
										  }
										@endphp
										
										<div class="col-md-3">
											@if(isset($address->city) && !is_object($address->city) )
											  {{ $address->city }}
											@else
											  &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed city");
										  }
										@endphp
										
										<div class="col-md-1">
											@if(isset($address->state) && !is_object($address->state) )
											  {{ $address->state }}
											@else
											  &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed state");
										  }
										@endphp
										
										<div class="col-md-1">
											@if(isset($address->zip) && !is_object($address->zip) )
											  {{ $address->zip }}
											@else
											  &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed zip");
										  }
										@endphp
										
										<div class="col-md-2">
											@if(isset($address->county) && !is_object($address->county) )
											  {{ $address->county }}
											@else
											  &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed county");
										  	Log::info("Show firstDate");
										  	Log::info(gettype($address->firstDate));
										  }
										@endphp
										
										<div class="col-md-1">
											@if(isset($address->firstDate) && !is_object($address->firstDate) )
												{{ $address->firstDate }}
											@else
											    &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed firstDate");
										  	Log::info("Show lastDate");
										  	Log::info(gettype($address->lastDate));
										  }
										@endphp
										
										<div class="col-md-1">
											@if(isset($address->lastDate) && !is_object($address->lastDate) )
												{{ $address->lastDate }}
											@else
											    &nbsp;
											@endif
										</div>
										
										@php
										  if($testing){
										  	Log::info("Showed lastDate");
										  }
										@endphp

										
									@endif
									
								@endforeach
							
							
						
							{{-- END ADDRESS --}}
							
						</div>{{-- CLOSE PANEL-CONTAINER --}}
							
					@endif
					{{--  END PARSED ADDRESSES --}}
					
					
					{{-- ALIASES --}}
					@if(isset($aliases))
						
						<h4>Aliases</h4>
						<div class="panel-container">			
						@foreach($aliases as $a)
							{{ $a->FirstName . " " . $a->MiddleName . " " . $a->LastName}} <br>
					    @endforeach
					    </div>
						
					@endif
				    {{-- END ALIASES --}}
				   
					{{-- ALERTS --}}
					{{-- 
				    @if(isset($r->InstantCriminalResponse->Alerts))
				    	<h5 class="subheading">Alerts</h5>
				    	<div class="panel-container">
				    	@foreach($r->InstantCriminalResponse->Alerts as $alert)
				    	    {{ $alert }} <br>
				    	@endforeach
				    	</div>
				    @endif
				    --}}
				    {{-- END ALERTS --}}
				    
				    @if(isset($r->InstantCriminalResponse->OffenderCount) && $r->InstantCriminalResponse->OffenderCount == 0)
					  <h4>No Criminal Records Found</h4>
				    @elseif(isset($r->InstantCriminalResponse->OffenderCount) && $r->InstantCriminalResponse->OffenderCount > 0 )
				    
				        @php
						  if($testing){
						  	Log::info("Show Offender records");
						  }
						@endphp
				    
				        <h4>Criminal Search</h4>
				    	<div class="panel-container">
				    		
				    		@php
				    		
				    		  if($testing){
				    		  	Log::info("Offender Count: " . $r->InstantCriminalResponse->OffenderCount);
				    		  	Log::info("Type is " . gettype($r->InstantCriminalResponse->OffenderCount));
				    		  }
				    		
				    		  if($r->InstantCriminalResponse->OffenderCount == 1){
				    		  	$offense[] = $r->InstantCriminalResponse->Offender;
				    		  }else{
				    		  	$offense = $r->InstantCriminalResponse->Offender;
				    		  }
				    		  
				    		  $recordCount = 0;

				    		@endphp
				    		
				    		{{-- OFFENDER --}}
				    		
				    		@foreach($offense as $o)

				    		    <?php
				    		    
					    		    if($testing){
					    		      Log::info(json_encode($o));
									}
									
									
									if(!$isAdmin && isset( $o->meta) && $o->meta->hide == true){
										Log::info("Hide the record");
										continue;
									} 
									
									
									$checked = ( isset( $o->meta) && $o->meta->hide == true) ? 'checked' : '';
	
				    		    ?>
				    		    
				    		    <h5 class="subheading">Record<span class="edit_control_wrapper edit-button-row">Hide this: <input onchange="Admin.toggleDataDiverRecord({{ $recordCount }}, this)" class="edit_control_checkbox" type="checkbox" {{ $checked }}></span></h5>
				    		    
				    		    {{----------------------------------------------------------}}
								{{---------------------- ADMIN RELATED ROWS ----------------}}
								{{--------------------------- RECORDS ----------------------}}
								{{----------------------------------------------------------}}
								
								@if($isAdmin)
				    		    
	                                  @php
	                                  
	                                    if($testing){
	                                    	Log::info("Show the edit/admin related roles");
	                                    }
	                                  
					    		        $style = ( isset( $o->meta) && $o->meta->hide == true) ? 'block' : 'none';
					    		      @endphp
	
					    		      <div class="admin-meta-info-wrapper admin-meta-info-wrapper_{{ $recordCount }}" style="display:{{ $style }};">
					    		        <div class="line admin-meta-info admin-meta-info-name">
											<div class="col-md-4">
												<strong>Hidden By: </strong> 
											</div>
											<div class="col-md-8 meta-admin-name_{{$recordCount}}">
												@if( isset( $o->meta) && isset($o->meta->admin) )
												{{ $o->meta->admin }}
												@else
												  &nbsp;
												@endif
											</div>
										</div>
										
										<div class="line admin-meta-info admin-meta-info-date">
											<div class="col-md-4">
												<strong>Date Modified: </strong> 
											</div>
											<div class="col-md-8 meta-admin-date_{{$recordCount}}">
												@if( isset( $o->meta) && isset($o->meta->date) )
												  {{ $o->meta->date }}
												@else
												  &nbsp;
												@endif
											</div>
										</div>
										
					    		    </div>
					        	@endif
					        	
					        	{{----------------------------------------------------------}}
					        	{{----------------------------------------------------------}}
					        	
					        	@php
				    		      $mName = is_string($o->MiddleName) ? $o->MiddleName : "";
				    		    @endphp
				    		    
				    		    @if(isset($o->FirstName))
								<div class="line">
									<div class="col-md-4">
										<strong>Offender Name</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->FirstName . " " . $mName . " " . $o->LastName }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("Displayed Name"); } @endphp
								
								@if(isset($o->DOB))
								<div class="line">
									<div class="col-md-4">
										<strong>Offender DOB</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->DOB }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After DOB"); } @endphp
				    		
				    			@if(isset($o->Race))
								<div class="line">
									<div class="col-md-4">
										<strong>Offender Race</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->Race }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After Race"); } @endphp
								
								@if(isset($o->Sex))
								<div class="line">
									<div class="col-md-4">
										<strong>Offender Sex</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->Sex }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After Gender"); } @endphp
					    		
					    		
					    		@if(isset($o->MatchType))
								<div class="line">
									<div class="col-md-4">
										<strong>Match Type</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->MatchType }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After matchType"); } @endphp
								
								@if(isset($o->SourceOfRecord))
								<div class="line">
									<div class="col-md-4">
										<strong>Source Of Record</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->SourceOfRecord }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After Source of Record"); } @endphp
								
								@if(isset($o->StateOfRecord) && !is_object($o->StateOfRecord))
								<div class="line">
									<div class="col-md-4">
										<strong>State Of Record</strong> 
									</div>
									<div class="col-md-8">
										{{ $o->StateOfRecord }}
									</div>
								</div>
								@endif
								
								@php if($testing){ Log::info("After State of Record");} @endphp
								
								{{-- RECORDS --}}
								
								@php
								
								  if($testing){
								  	Log::info("Checking Offense records.");
								  }
								
								  $recordArray = $o->Records;
								  
								  
								  if($testing){
								  	Log::info("I have a recordArray of type " . gettype($recordArray));
								  }
								  
								  $offenseCount = 0;
								  
								@endphp
								
								{{----------------------------------------------------------}}
								{{----------------------- PROCESS OFFENSES -----------------}}
								{{----------------------------------------------------------}}
								
								
								
								@foreach($recordArray as $record=>$value)
								
									@php
								      if(gettype($value) == "object"){
									      $value = array($value);
									  }
								  	@endphp
								  	
								  	@foreach($value as $rec)
								  	
								  		{{----------------------------------------------------------}}
								  		@php
									      if($testing){
									      	Log::info("Processing rec");
									      }
									    
									      if(!$isAdmin && isset( $rec->meta) && $rec->meta->hide == true) {	
									        Log::info("This record is hidden");
									      	continue;    	
									      }
								    
								          if($testing){
									      	Log::info("Processing whether to hide the edit controls");
									      }
									    
										  $checked = ( isset( $rec->meta) && $rec->meta->hide == true) ? 'checked' : '';
										  
										@endphp

									    <div class="line minor-subcategory">
											<div class="col-md-4">
												<strong>OFFENSE</strong> 
											</div>
											<div class="col-md-8">
												<span class="edit_control_wrapper edit_offense_wrapper edit-button-row">
													Hide this: 
													<input onchange="Admin.toggleDataDiverOffense({{ $recordCount }}, {{ $offenseCount }}, this )" class="edit_control_checkbox edit_offense" type="checkbox" {{ $checked }}>
												</span>
											</div>
										</div>
										
										{{----------------------------------------------------------}}
										{{---------------------- ADMIN RELATED ROWS ----------------}}
										{{--------------------------- OFFENSES ---------------------}}
										{{----------------------------------------------------------}}
										
										@if($isAdmin)
					    		    
						    		      @php
						    		      
						    		        if($testing){
						    		        	Log::info("User isAdmin");
						    		        }
						    		      
						    		        $style = ( isset( $rec->meta) && $rec->meta->hide == true ) ? 'block' : 'none';
						    		      @endphp
					    		    
						    		      <div class="admin-meta-info-wrapper admin-meta-info-wrapper_{{ $recordCount }}_{{ $offenseCount }}" style="display:{{ $style }};">
							    		        <div class="line admin-meta-info admin-meta-info-name">
													<div class="col-md-4">
														<strong>Hidden By: </strong> 
													</div>
													<div class="col-md-8 offense-meta-admin-name_{{$recordCount}}_{{$offenseCount}}">
														@if( isset( $rec->meta) && $rec->meta->admin )
														  {{ $rec->meta->admin }}
														@else
														  &nbsp;
														@endif
													</div>
												</div>
												
												<div class="line admin-meta-info admin-meta-info-date">
													<div class="col-md-4">
														<strong>Date Modified: </strong> 
													</div>
													<div class="col-md-8 offense-meta-admin-date_{{$recordCount}}_{{$offenseCount}}">
														@if( isset( $rec->meta) && $rec->meta->date )
															{{ $rec->meta->date }}
														@else
														  &nbsp;
														@endif
													</div>
												</div>
						    		      </div>
						    		      
					    		    
					    		     	@endif 
										{{-- END ADMIN RELATED ROWS--}}
										
										@if(isset($rec->Offense))
										<div class="line">
											<div class="col-md-4">
												<strong>Offense Description</strong> 
											</div>
											<div class="col-md-8">
												
												@if(is_string($rec->Offense))
												
												  {{ $rec->Offense }}
												  
												@elseif(is_array($rec->Offense))
												
												  @foreach($rec->Offense as $off)
												    {{ $off }}<br>
												  @endforeach
												@endif
											</div>
										</div>
										@endif
										
										@if($testing) @php Log::info("After Offense Description"); @endphp @endif
								  	
								  		
									  	@if(isset($rec->Statute))
										<div class="line">
											<div class="col-md-4">
												<strong>Statute</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->Statute }}
											</div>
										</div>
										@endif
										
										@if($testing)  After Statute <br> @endif
										
										
										
										@if(isset($rec->CourtData->CourtDetail))
										<div class="line">
											<div class="col-md-4">
												<strong>Court Detail</strong> 
											</div>
											<div class="col-md-8">
												
												@php
												  $message = "";
												  
												  if(gettype($rec->CourtData->CourtDetail) == "array"){
												  	foreach($rec->CourtData->CourtDetail as $m){
													  	$message .= $m . ", ";
													}
												  }else{
												  	$message = $rec->CourtData->CourtDetail;
												  }
												  
												@endphp
												
												{{ rtrim($message, ", ") }}
												
											</div>
										</div>
										
										@endif
										
										@if($testing)  After Court Detail  @endif
										
										@if(isset($rec->FileDate))
										<div class="line">
											<div class="col-md-4">
												<strong>Case Filing Date</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->FileDate }}
											</div>
										</div>
										@endif
										
										@if($testing)  After File Date <br> @endif
										
										@if(isset($rec->FileID))
										<div class="line">
											<div class="col-md-4">
												<strong>Case File ID</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->FileID }}
											</div>
										</div>
										@endif
										
										@if($testing)  After Case File ID <br> @endif
										
										
										@if(isset($rec->CaseNumber))
										<div class="line">
											<div class="col-md-4">
												<strong>Case Number</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->CaseNumber }}
											</div>
										</div>
										@endif
										
					    		    	@if($testing)  After Case Number <br> @endif
					    		    	
					    		    	@if(isset($rec->OffenseCounty))
										<div class="line">
											<div class="col-md-4">
												<strong>Jurisdiction</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->OffenseCounty }}
											</div>
										</div>
										@endif
					    		    	
					    		    	@if($testing)  After Offense County <br> @endif
					    		    	
					    		    	
					    		    	
					    		    	@if(isset($rec->Disposition))
										<div class="line">
											<div class="col-md-4">
												<strong>Disposition</strong> 
											</div>
											<div class="col-md-8">
												
												@if(is_string($rec->Disposition))
												    {{ $rec->Disposition }}
												@elseif(is_array($rec->Disposition))
												
												  @php
												  
												    $disp = implode(", " , $rec->Disposition);
												
												  @endphp
												  
												  {{ $disp }}
												  
												@endif
											</div>
										</div>
										@endif
										
										@if($testing)  After Disposition <br> @endif
										
										@if(isset($rec->DispositionDate))
										<div class="line">
											<div class="col-md-4">
												<strong>Disposition Date</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->DispositionDate }}
											</div>
										</div>
										@endif
										
										@if($testing)  After Disposition Date<br> @endif
										
										@if(isset($rec->Fine))
										<div class="line">
											<div class="col-md-4">
												<strong>Fine</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->Fine }}
											</div>
										</div>
										@endif
										
										@if($testing)  After Disposition Fine<br> @endif
										
										@if(isset($rec->Sentence))
										<div class="line">
											<div class="col-md-4">
												<strong>Sentence</strong> 
											</div>
											<div class="col-md-8">
												{{ $rec->Sentence }}
											</div>
										</div>
										@endif
										
										@if($testing)  After Sentence<br> @endif
								  	
								  	
								  	
								
								
										<?php $offenseCount ++; ?>
										
									@endforeach
									{{-- end foreach value as record --}}
								
								
								@endforeach
								
								<?php $recordCount ++; ?>
								
					    	@endforeach
					    	{{-- END OFFENDER --}}
					    	
					    	

				       </div>{{-- CLOSE PANEL-CONTAINER --}}
				    @endif
				    {{-- END OFFENSES --}}
	
				</div>	
			</div>
		</div>
	</div>