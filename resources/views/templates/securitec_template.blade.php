<?php

Log::info("Display Securitec record");

$r = json_decode(decrypt($report -> report));

//Log::info(gettype($r));

$testing = false;
//$testing = true;


if($testing){
	
	Log::info("************  Report **************************************");
	//$s = print_r($report, true);
    //Log::info($s);
	

	Log::info("************  Decrypted Report  ****************************");
	$s = print_r($r, true);
    Log::info($s);
}


$location = null;


if($testing){
	Log::info("report->check_type is " . $report->check_type);
}


if( ($report->check_type == 3 || $report->check_type == 6 ) && isset($report->state) ){
	
	if($testing){
		Log::info("Checking Report->State");
		Log::info($report->state);
	}
	
	$state = App\Models\State::find($report->state);
	$location = ": " . $state->title;
	
}elseif($report->check_type == 4 && isset($report->county)){
	
	if($testing){
		Log::info("Checking Report->County");
		Log::info($report->county);
	} 

	$county = App\Models\County::find($report->county);
	$location = ": " . $county->title . ",  " . $county->state_code;
	
}elseif($report->check_type == 7 && isset($report->district)){
	
	if($testing){
		Log::info("Checking Report->District");
		Log::info($report->district);
	}
	
	$district = App\Models\District::find($report->district);
	$location = ": " . $district->title  . ",  " . $district->state_code;
}

if($testing){
	Log::info("Location is $location");
}

//$location = "";

?>

<div class="row report_template">
	<div class="col-md-12">
		<div class="panel panel-default">

			@if( isset($r->Screening) && $r->Screening->ScreeningStatus->ResultStatus != 'Hit')
			<div class="panel-body">
				@if(isset($location))
				  <h4>No records found for {{ $location }}</h4>
				@else
				  <h4>No records found.</h4>
				@endif
			</div>

			@elseif( isset($r->tracking) )
			<div class="panel-body">
				<h4>{{ $r->message }}  {{$location}}</h4>
			</div>

			@else
			<div class="panel-body">

				<h4>Criminal Search</h4>

				@if(isset($r->Screening->CriminalReport))
				
					@php
						if($testing) {Log::info("Criminal Case is set");}
		
						if(gettype($r->Screening->CriminalReport->CriminalCase) == 'array'){
						  $case = $r->Screening->CriminalReport->CriminalCase;
						}elseif(gettype($r->Screening->CriminalReport) == 'object'){
						  $case[] = $r->Screening->CriminalReport->CriminalCase;
						}else{
						  $case = [];
						}
					@endphp
					
					
					
					@foreach($case as $c)
					
					    @php 
					        if($testing) {
					        	Log::info("********* Parsing Case ");
					        }  
					    @endphp
					
						<h5 class="subheading">Record</h5>
					
					    {{-- 
						@if(isset($c->AdditionalItems) && is_array($c->AdditionalItems))
						<div class="line">
							<div class="col-md-4">
								<strong>Court</strong>
							</div>
							<div class="col-md-8">
								
								@php
								
									try{
										echo $c->AdditionalItems[0]->Text;
									}catch(\Exception $e){
										&nbsp;
									}
																	
								@endphp

							</div>
						</div>
						@endif
						--}}
						
						@php if($testing){ Log::info("After AdditionalItems"); } @endphp

						@if(!is_object($c->ArrestingAgency) && isset($c->ArrestingAgency))
						<div class="line">
							<div class="col-md-4">
								<strong>Agency</strong>
							</div>
							<div class="col-md-8">
								{{ $c->ArrestingAgency }}
							</div>
						</div>
						@endif
						
						@php if($testing){ Log::info("After ArrestingAgency"); } @endphp

						@if(isset($c->AgencyReference))
						<div class="line">
							<div class="col-md-4">
								<strong>Case Number</strong>
							</div>
							<div class="col-md-8">
								{{ $c->AgencyReference->IdValue }}
							</div>
						</div>
						@endif
						
						@php 
							if($testing){ 
								Log::info("After AgencyReference");
								Log:info("Type of caseFileDate : " . gettype($c->CaseFileDate));
								Log::info(json_encode($c->CaseFileDate));
							} 
						@endphp

						@if(isset($c->CaseFileDate) && !is_object($c->CaseFileDate->AnyDate))
						<div class="line">
							<div class="col-md-4">
								<strong>Case File Date</strong>
							</div>
							<div class="col-md-8">
								{{ $c->CaseFileDate->AnyDate }}
							</div>
						</div>
						@endif
						
						@php 
						  if($testing){
							Log::info("After CaseFileDate"); 
						    Log::info(json_encode($c->SubjectIdentification));
						  }
						    
						@endphp

						@if(isset($c->SubjectIdentification))
						<div class="line">
							<div class="col-md-4">
								<strong>Full Name</strong>
							</div>
							<div class="col-md-8">
								
								@php
								 
								   $givenName = !is_object($c->SubjectIdentification->PersonName->GivenName) ? $c->SubjectIdentification->PersonName->GivenName : "";
								   $middleName = !is_object($c->SubjectIdentification->PersonName->MiddleName) ? $c->SubjectIdentification->PersonName->MiddleName : "";
								   $lastName = !is_object($c->SubjectIdentification->PersonName->FamilyName) ? $c->SubjectIdentification->PersonName->FamilyName : "";
								
								@endphp
								
								{{ $givenName . " " . $middleName . " " . $lastName }}

							</div>
						</div>
						@endif
						
						@php if($testing){ Log::info("Displayed SubjectIdentification"); } @endphp
						
						{{-- ************************************************** --}}
						
						@php
						  if(is_array($c->Charge)){
						  	$charges = $c->Charge;
						  }elseif(is_object($c->Charge)){
						  	$charges[] = $c->Charge;
						  }else{
						  	$charges = [];
						  }
						@endphp
						
						@foreach($charges as $charge)
						
						   @php if($testing){ Log::info("Displaying Charges"); Log::info(json_encode($charge)); } @endphp
							
							<div class="minor-subcategory">Charge</div>
							    
						    @if(isset($charge->ChargeOrComplaint))
							<div class="line">
								<div class="col-md-4">
									<strong>Charge or Complaint</strong> 
								</div>
								<div class="col-md-8">
									{{ $charge->ChargeOrComplaint }}
								</div>
							</div>
							@endif
							
							@php if($testing){ Log::info("After ChargeOrComplaint"); } @endphp
							
							@if(isset($charge->ChargeTypeClassification))
							<div class="line">
								<div class="col-md-4">
									<strong>Charge Classification</strong> 
								</div>
								<div class="col-md-8">
									{{ $charge->ChargeTypeClassification }}
								</div>
							</div>
							@endif
							
							@php 
							
								if($testing){ 
									Log::info("After ChargeTypeClassification");
									//Log::info("AnyDate:  " . $charge->OffenseDate);
									
									
							     } 
							     
							     //Log::info($charge->OffenseDate->AnyDate);
							     
							@endphp
							
							{{-- 
							@if(isset($charge->OffenseDate->AnyDate ))
							<div class="line">
								<div class="col-md-4">
									<strong>Offense Date</strong> 
								</div>
								<div class="col-md-8">
									{{ $charge->OffenseDate->AnyDate }}
								</div>
							</div>
							@endif
							--}}
							
							@php if($testing){ Log::info("After OffenseDate->AnyDate"); } @endphp
							
							@if(isset($charge->Sentence))
							<div class="line">
								<div class="col-md-4">
									<strong>Sentence</strong> 
								</div>
								<div class="col-md-8">
										<?php 
											$sentence = json_encode($charge->Sentence);
											$sentence = str_replace(array("\\n", "\r"), ', ', $sentence);
											$sentence = str_replace(array("\"", "}", "{"), '', $sentence);
											$sentence = str_replace(array("\\"), '', $sentence);
											//$sentence = str_replace(":", "&colon;", $charge->Sentence);
											
											if(strlen($sentence) == 0) $sentence = "&nbsp;";
										 ?>
 
										{{ rtrim($sentence, ", ") }}
								</div>
							</div>
							@endif
							
							@php if($testing){ Log::info("After Sentence"); } @endphp
							
							@if(isset($charge->Disposition))
							<div class="line">
								<div class="col-md-4">
									<strong>Disposition</strong> 
								</div>
								<div class="col-md-8">
									{{ $charge->Disposition }}
								</div>
							</div>
							@endif
							
							@php 
							    if($testing){ 
							    	Log::info("After Disposition"); 
							    	Log::info(json_encode($charge->DispositionDate->AnyDate));
							    }     
							@endphp
							
							@if(!is_object($charge->DispositionDate->AnyDate) && isset($charge->DispositionDate->AnyDate ))
							<div class="line">
								<div class="col-md-4">
									<strong>Disposition Date</strong> 
								</div>
								<div class="col-md-8">
									{{ $charge->DispositionDate->AnyDate }}
								</div>
							</div>
							@endif
							
							@php if($testing){ Log::info("After DispositionDate->AnyDate"); } @endphp

						@endforeach {{-- $charges as $charge--}}
						
					@endforeach {{-- case as c --}}
					
			    @endif {{--  end if isset Screening->CriminalReport --}}

			</div>{{-- END panel-body --}}
			@endif

		</div>
	</div>
</div>

