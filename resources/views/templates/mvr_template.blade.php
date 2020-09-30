<?php

  //echo "Load the template<br>";
  //return;
  //echo json_encode($report->report) . "<br>";
  
  
  //echo gettype($report) . "<br>";
  
  //return;
  
  $mvrCheck = decrypt($report->report);
  $mvrCheck = json_decode($mvrCheck);
  
  $r = [];
  
  //echo json_encode($mvrCheck) . "<br>";
  //echo gettype($mvrCheck) . "<br>";
  
  //return;
  
  //This should be a driver not found error.
  if(gettype($mvrCheck) == "NULL"){
 	echo decrypt($report->report);
	return;
  }elseif(is_string($mvrCheck) ){
  	
	echo $mvrCheck . "<br>";
	return;
	
  //}elseif(!isset($mvrCheck->DlRecord) || !is_string($mvrCheck)){
  }elseif(!isset($mvrCheck->DlRecord)){
  	
  	$str = "";
	
	foreach($mvrCheck as $key=>$val){
		$str .= $val . "<br>";
	}
	
	echo $str;
	return;
  }else{
  	$r = $mvrCheck;
  }
  
  
  
  $testing = false;
  //$testing = true;

  if($testing){
  	Log::info("============================== TESTING MODE  ==========================================\n");
  	$s = print_r($r, true);
    Log::info($s);
    Log::info("=======================================================================================\n");
  }

  $valid = "";
  
  if(isset($r->DlRecord)){  //this will not be set if there was an error
  	$valid = $r->DlRecord->Result->Valid;
  }else{
  	$valid = "N";
  }
  
  //Log::info("Valid = " . $valid);
  
  //if(Auth::user()->hasRole('admin')){
		//Log::info("This is an admin role");
  //}
  
  $location = null;
  

  if( $report->check_type == 10 && isset($report->state) ){
	  $state = App\Models\State::find($report->state);
	  $location = ": " . $state->title;
  }

?>

<script>



</script>

@if($valid == "N")
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">

					@if(isset($location))
					  <strong>There was an error with your request for {{ $location }}</strong>
					@else
					  <strong>There was an error with your request</strong>
					@endif
					<br>
					
					@if( isset($r->DlRecord) )
					  {{ $r->DlRecord->Result->ErrorDescription }}
					@else
					  @php
					  
					    $msg  = "";
					  
					    if(is_string($r)){
					    	$msg = $r;
					    }else{
					    	
					    	foreach($r as $rObj=>$val){
						    	$msg .= $val;
						    }
						    
					    }
					  
					  @endphp
					  
					  {{ $msg }}
					@endif
				</div>
			</div>
		</div>
	</div>
@elseif(!isset($r->DlRecord))
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					@if(isset($location))
					  MVR Record is pending for {{ $location }}.
					@else
					  MVR Record is pending.
					@endif
				</div>
			</div>
		</div>
	</div>
@else

    @if(Auth::user()->hasRole('admin'))
    {{-- 
    <button onclick="Admin.editCheck('mvr-report')" class="btn btn-success edit-check">
		<i class="fa fa-print" aria-hidden="true"></i>
		&nbsp;Edit Check
	</button>
	--}}
	
	
    @endif

	<div class="row report_template">
		<div class="col-md-12">
			
			<div class="panel panel-default">
				
				<div class="panel-body">
					
					<div class="panel-container">
						<h4>Current License</h4>
						
						<div class="line">
							<div class="col-md-4">
								<strong>State:</strong> 
							</div>
							<div class="col-md-8">
								{{ $r->DlRecord->Criteria->State->Full }}
							</div>
						</div>
						
						<div class="line">
							<div class="col-md-4">
								<strong>License Number:</strong> 
							</div>
							<div class="col-md-8">
								{{ $r->DlRecord->Criteria->LicenseNumber }}
							</div>
						</div>
						
						@php
						/*
						  if($r->DlRecord->CurrentLicense){
						  	$licenseRecord = (array)$r->DlRecord->CurrentLicense;
						  }else if($r->DlRecord->LicenseHistoryList){
						  	$licenseRecord = $r->DlRecord->LicenseHistoryList->LicenseItem;
						  }
						*/
						@endphp

						@if(isset($r->DlRecord->CurrentLicense)) 
							<div class="line">
								<div class="col-md-4">
									<strong>License Type:</strong> 
								</div>
								<div class="col-md-8">
									{{ $r->DlRecord->CurrentLicense->Type }}
								</div>
							</div>
									
							@if(isset($r->DlRecord->CurrentLicense->ClassDescription))
							<div class="line">
								<div class="col-md-4">
									<strong>Class:</strong> 
								</div>
								<div class="col-md-8">
									{{ $r->DlRecord->CurrentLicense->ClassDescription }}
								</div>
							</div>
							@endif
								
							@if(isset($r->DlRecord->CurrentLicense->ClassCode))
							<div class="line">
								<div class="col-md-4">
									<strong>Class Code:</strong> 
								</div>
								<div class="col-md-8">
									{{ $r->DlRecord->CurrentLicense->ClassCode }}
								</div>
							</div>
							@endif
								
							@if(isset($r->DlRecord->CurrentLicense->IssueDate))
							<div class="line">
								<div class="col-md-4">
									<strong>Issue Date:</strong> 
								</div>
								<div class="col-md-8">
									{{ $r->DlRecord->CurrentLicense->IssueDate->Day . "/" .  $r->DlRecord->CurrentLicense->IssueDate->Month . "/" . $r->DlRecord->CurrentLicense->IssueDate->Year}}
								</div>
							</div>
							@endif
							
							@if( isset($r->DlRecord->CurrentLicense->ExpirationDate) )
							<div class="line">	
								<div class="col-md-4">
									<strong>Expiration Date:</strong> 
								</div>
								<div class="col-md-8">
									{{ $r->DlRecord->CurrentLicense->ExpirationDate->Day . "/" .  $r->DlRecord->CurrentLicense->ExpirationDate->Month . "/" . $r->DlRecord->CurrentLicense->ExpirationDate->Year}}
								</div>
							</div>
							@endif
							
							@if(isset($r->DlRecord->CurrentLicense->PersonalStatusList))
							
							@php
							
							  $status = "";
							  
							  
							  try{
							  	
							  	$stat = $r->DlRecord->CurrentLicense->PersonalStatusList->StatusItem;
							  						  	
							  	//Log::info( gettype($stat) );
	
							  	if(is_array($stat)){
							  		//Log::info("Is array");
							  		
							  		foreach($stat as $st){
							  			$status .= $st->Name . ", ";
							  		}
							  		
							  		$status = rtrim($status, ", ");
		
							  	}else if(is_object($stat)){
							  		//Log::info(json_encode($stat));
							  		$status = $stat->Name;
							  	}
							  	
							  }catch(\Exception $e){
							  	Log::info("Died in personal status list");
							  }
							  
							@endphp
							
							<div class="line">
								<div class="col-md-4">
									<strong>Personal License Status:</strong> 
								</div>
								<div class="col-md-8">
									{{ $status }}
								</div>
							</div>
							@endif
								
							@if(isset($r->DlRecord->CurrentLicense->CommercialStatusList))
							<div class="line">
								<div class="col-md-4">
									<strong>Commercial License Status:</strong> 
								</div>
								<div class="col-md-8">
									
									
									@php
									$status = "";
									
									
									try{
										
										$items = $r->DlRecord->CurrentLicense->CommercialStatusList->StatusItem;
										
										if(is_array($items)){
											
											foreach($items as $item){
												$status .= $item->Name . ", ";
											}
											
											$status = rtrim($status, ", ");
											
										}else if(is_object($items)){
											$status = $items->Name;
										}else{
											Log::info("Commercial Status");
											Log::info(gettype($items));
										}
										
									}catch(\Exception $e){
										
										
									}
									
									
									@endphp
									
									{{ $status }} 
								</div>
							</div>
							@endif
							
							@if(isset($r->DlRecord->CurrentLicense->EndorsementList))
							<div class="line">
								<div class="col-md-4">
									<strong>Endorsements:</strong> 
								</div>
								<div class="col-md-8">
									
									@php
									
									  $endorsement = "";
	
									  $items = $r->DlRecord->CurrentLicense->EndorsementList->EndorsementItem;
									 
									  try{
									  	
									  	if(is_array($items)){
									  		
									  		//Log::info("Is array");
									  		//Log::info(json_encode($items));
									  		
									  		foreach($items as $i){
									  			$endorsement .= $i->Name . ", ";
									  		}
									  		
									  		$endorsement = rtrim($endorsement, ", ");
									  		
									  	}else{
									  		Log::info("ENDORSEMENT OBJECT");
									  		Log::info( gettype($items) );
									  	}
									  	
									  }catch(\Exception $e){
									  	
									  	
									  }
									  
									  
									@endphp
									
									
									{{ $endorsement }}
									
								</div>
							</div>
							@endif
	
							@if( isset($r->DlRecord->CurrentLicense->RestrictionList))
							<div class="line">
								<div class="col-md-4">
									<strong>Restrictions:</strong> 
								</div>
								<div class="col-md-8">
									
									@php
									
									  $restriction = "";
									  
									  if( is_array($r->DlRecord->CurrentLicense->RestrictionList->RestrictionItem)){
									  	
									  	foreach($r->DlRecord->CurrentLicense->RestrictionList->RestrictionItem as $item){
									  		$restriction .= $item->Name . ", ";
									  	}
									  	
									  	$restriction = rtrim($restriction, ", ");
									  	
									  }else{
									  	$restriction = $r->DlRecord->CurrentLicense->RestrictionList->RestrictionItem->Name;
									  }
									
									@endphp
									
									{{ $restriction }}
								</div>
							</div>
							@endif
					    @endif	
							
						@if(isset($r->DlRecord->MessageList))
						
							@php
							
							$message = "";
							
							
							foreach($r->DlRecord->MessageList->MessageItem as $messages){
								
								//Log::info(json_encode($messages));
								
								//$s = print_r($messages, true);
								//Log::info($s);
								//Log::info(gettype($messages));
								
								try{
									
									if(is_string($messages)){
										//Log::info("STRING " . $messages);
										$message = $messages;
									}
									
									if( is_array($messages) ){
										
										foreach($messages as $m){
											//$message .= $m->Line . ", ";
											$message .= $m . ", ";
										}
										
										$message = rtrim($message, ", ");
										
										//Log::info("ARRAY " . json_encode($messages));
										//$message = implode(", ", $messages->Line);
										
										
									}
									
									if(is_object($messages)){
										//Log::info("OBJECT " . json_encode($messages));
										//$message = $messages->Line;
										if(is_array($messages->Line)){
											$message = implode(", ", $messages->Line);
										}else if(is_string($messages->Line)){
											$message = $messages->Line;
										}
									}
									
								}catch(\Exception $e){
									Log::info("Messages is a different data type");
									//Log::info(gettype($messages));
								}
								
							
							@endphp
						
							<div class="line">
								<div class="col-md-4">
									<strong>Messages:</strong> 
								</div>
								<div class="col-md-8">
									{{ $message }}
								</div>
							</div>
							
							@php } @endphp
							
						@endif
						
						
					</div>
						
					@if(isset($r->DlRecord->MedicalCertificateList))
					<div class="panel-container">
						<h4>Medical Certifications</h4>
						
						<div class="col-md-3">
							<h5 class="subheading">Status</h5>
						</div>
						
						<div class="col-md-3">
							<h5 class="subheading">Issue Date</h5>
						</div>
						
						<div class="col-md-3">
							<h5 class="subheading">Expiration Date</h5>
						</div>
						
						<div class="col-md-3">
							<h5 class="subheading">Type</h5>
						</div>
						
						
						<div class="col-md-3">
							@if(isset($r->DlRecord->MedicalCertificateList->MedicalCertificateItem->Status))
								{{ $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->Status }}
							@else
							  &nbsp;
							@endif
						</div>
						
						<div class="col-md-3">
							@if(isset($r->DlRecord->MedicalCertificateList->MedicalCertificateItem->IssueDate))
								{{ $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->IssueDate->Day . "/" .  $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->IssueDate->Month . "/" . $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->IssueDate->Year }}
							@else
							  &nbsp;
							@endif
						</div>
						
						<div class="col-md-3">
							@if(isset($r->DlRecord->MedicalCertificateList->MedicalCertificateItem->ExpirationDate))
								{{ $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->ExpirationDate->Day . "/" .  $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->ExpirationDate->Month . "/" . $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->ExpirationDate->Year }}
							@else
							  &nbsp;
							@endif
						</div>
						
						<div class="col-md-3">
							@if(isset($r->DlRecord->MedicalCertificateList->MedicalCertificateItem->SelfCertification))
								{{ $r->DlRecord->MedicalCertificateList->MedicalCertificateItem->SelfCertification->Type }}
							@else
							  &nbsp;
							@endif
						</div>
					</div>
					@endif
					
					@if(isset($r->DlRecord->LicenseHistoryList))
						<div class="panel-container">
							<h4>License History</h4>
							
							<div class="col-md-4">
								<h5 class="subheading">Type</h5>
							</div>
							
							<div class="col-md-4">
								<h5 class="subheading">Class</h5>
							</div>
							
							<div class="col-md-4">
								<h5 class="subheading">Issue Date</h5>
							</div>
							
							<?php
							
							//echo gettype($r->DlRecord->LicenseHistoryList->LicenseItem);
							
							    if(is_array($r->DlRecord->LicenseHistoryList->LicenseItem)){
							    	$history = $r->DlRecord->LicenseHistoryList->LicenseItem;
									//echo "Is array";
							    }else{
							    	//echo "is object";
							    	$history[] = $r->DlRecord->LicenseHistoryList->LicenseItem;
							    }
							    
								//print_r($history);
								
							     
							?>
					    	
					    	@foreach($history as $h)
					    	
						    		<div class="col-md-4">
						    			@if(isset($h->Type))
										{{ $h->Type }}
										@endif
									</div>
									<div class="col-md-4">
										@if(isset($h->ClassDescription))
										{{ $h->ClassDescription }}
										@endif
									</div>
									<div class="col-md-4">
										@if(isset($h->IssueDate))
										{{ $h->IssueDate->Day . "/" . $h->IssueDate->Month . "/" . $h->IssueDate->Year}}
										@endif
									</div>
								
					    	@endforeach
					   </div>
				    @endif
				    
				    @if(isset($r->DlRecord->EventList))
			    		<div class="panel-container">
					    	<h4>Driving Records Search</h4>
					    	
					    	<?php 
					    		if(is_array($r->DlRecord->EventList->EventItem)){
					    			$events = $r->DlRecord->EventList->EventItem;
					    		}else{
					    			$events[] = $r->DlRecord->EventList->EventItem;
					    		}

					    	?>
					    	
					    	@foreach($events as $event)
					    		
					    		@if(isset($event->DescriptionList))			    	
						    		<div class="col-md-12">
										<h5 class="subheading">Record</h5>
									</div>
						    		
						    		@if(isset($event->Common->Subtype))
						    		<div class="col-md-4"><strong>Type</strong></div>
						    		<div class="col-md-8">
						    			{{ $event->Common->Subtype }}
						    		</div>
						    		@endif

									@if(isset($event->DescriptionList->DescriptionItem->StateDescription))
						    		<div class="col-md-4"><strong>Description</strong></div>
						    		<div class="col-md-8">
						    			{{ $event->DescriptionList->DescriptionItem->StateDescription }}
						    		</div>
						    		@endif
						    	
						    		@if(isset($event->Common->State))
							    		<div class="col-md-4"><strong>State</strong></div>
							    		<div class="col-md-8">
							    			{{ $event->Common->State->Full }}	
							    		</div>
							    	@endif
						    		
						    		@if(isset($event->Common->Date))
						    		<div class="col-md-4"><strong>Occurance Date</strong></div>
						    		<div class="col-md-8">
						    			{{ $event->Common->Date->Day . "/" . $event->Common->Date->Month . "/" . $event->Common->Date->Year}}
						    		</div>
						    		@endif

					    			@if(isset($event->Violation) && isset($event->Violation->ConvictionDate))
						    		<div class="col-md-4"><strong>Conviction Date</strong></div>
						    		<div class="col-md-8">
						    			{{ $event->Violation->ConvictionDate->Day . "/" . $event->Violation->ConvictionDate->Month . "/" . $event->Violation->ConvictionDate->Year}}
						    		</div>
						    		@endif
							    	
							    	@if(isset($event->Accident) && isset($event->Accident->ReportNumber) )
							    		<div class="col-md-4"><strong>Accident Report Number</strong></div>
							    		<div class="col-md-8">{{ $event->Accident->ReportNumber }}</div>
							    	@endif
						    	@endif
						    	
					    	@endforeach
						</div>
			    	@endif
			    	
				</div>
			</div>
		</div>
	</div>
	
	{{-- 
	@include('checks/reportEdit', ['reportType' => 'mvr_report'])
	--}}
	
@endif
