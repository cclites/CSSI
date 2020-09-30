<?php

  $r = json_decode(json_decode(decrypt($report->report)));
  
  //$r = App\Models\UsInfoSearch::standardize($report);
  //$r = json_decode($r);
  
  Log::info("Display the UsInfoCheck");
  Log::info(gettype($r));
  //$s = print_r($r, true);
  //Log::info($s);
  //Log:info("==========================================\n");
  
  $person = $r->people->person;

  return;
?>

@if( !isset($r->searchInput) )
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<strong>There was a problem with your request.</strong>
			</div>
		</div>
	</div>
</div>
@else
<div class="row report_template">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body">
				
				<h4>Names</h4>
				
				@foreach($person->names->name as $p)
				
				  @foreach($p as $n=>$k)  
				  
				      @if($n !== "suffix")
					  <div class="line">
						<div class="col-md-4">
							<strong>{{ $n }}</strong> 
						</div>
						<div class="col-md-8">
							{{ $k }}
						</div>
					  </div>
					  @endif
				  
				  @endforeach
				  
				@endforeach
				
				<h4>Shared SSNs</h4>
				
				@foreach($person->sharingSSNs->sharingSSN as $p)
				  <h5 class="subheading">Record</h5>
				  @foreach($p as $n=>$k)  
				      
				      @if($n !== "suffix" && $n !== "SSNRecord")
					  <div class="line">
						<div class="col-md-4">
							<strong>{{ $n }}</strong> 
						</div>
						<div class="col-md-8">
							{{ $k }}
						</div>
					  </div>
					  @elseif($n == "SSNRecord")
					  
					      @foreach($k as $key=>$value)
					          <div class="line">
								<div class="col-md-4">
									<strong>{{ $key }}</strong> 
								</div>
								<div class="col-md-8">
									{{ $value }}
								</div>
							  </div>
					      @endforeach
					      
					  @endif
				  
				  @endforeach
				  
				@endforeach
				
				<h4>AKA</h4>
				
				@php
				  $names = "";
				@endphp
				
				@foreach($person->akalist->AKA as $p)
				
				    @php
				    	$names .= $p->firstName . " " . $p->middleName . " " . $p->lastName . ", ";
				    @endphp
				
				@endforeach
				
				<div class="line">
					<div class="col-md-4">
						<strong>Names</strong> 
					</div>
					<div class="col-md-8">
						{{ rtrim($names, ", ") }}
					</div>
				 </div>
				  
				 <h4>SSNs</h4> 
				 
				  
				 @foreach($person->SSNs->SSNInfo as $p=>$v)
				 
				     @if(is_string($p))
				         
				         <div class="line">
							<div class="col-md-4">
								<strong>{{ $p }}</strong> 
							</div>
							<div class="col-md-8">
								{{ $v}}
							</div>
						 </div>
						 {{--
						 <div class="line">
							<div class="col-md-4">
								<strong>SSNState</strong> 
							</div>
							<div class="col-md-8">
								{{ $p->SSNState }}
							</div>
						 </div>
						 
						 <div class="line">
							<div class="col-md-4">
								<strong>SSNYears</strong> 
							</div>
							<div class="col-md-8">
								{{ $p->SSNYears }}
							</div>
						 </div>
					      --}}
				     @else
				         <h5 class="subheading">Record</h5>
				     	 @foreach($v as $n=>$k)
					         <div class="line">
								<div class="col-md-4">
									<strong>{{ $n }}</strong> 
								</div>
								<div class="col-md-8">
									{{ $k }}
								</div>
							 </div>
					      @endforeach
				     @endif
				     
				 @endforeach
				 
				 
				 
				 <h4>DOBs</h4>
				 @foreach($person->DOBs->DOB as $p)
			         <div class="line">
						<div class="col-md-4">
							<strong>Date:</strong> 
						</div>
						<div class="col-md-8">
							{{ $p }}
						</div>
					 </div>
				 @endforeach
				 
				 
				 <h4>Addresses</h4>

				 @foreach($person->addresses->address as $p)
				 
				     <h5 class="subheading">Record</h5>

				     @foreach($p as $n=>$k)
				     
				         @if(!is_object($k))
				         <div class="line">
							<div class="col-md-4">
								<strong>{{ $n }}</strong> 
							</div>
							<div class="col-md-8">
								{{ $k }}
							</div>
						 </div>
						 @endif
						 
				     @endforeach
				 @endforeach
				 
				 <h4>Relatives</h4>

				 @foreach($person->relatives->relative as $p)
				 
				     <h5 class="subheading">Record</h5>

				     @foreach($p as $n=>$k)
				         <div class="line">
							<div class="col-md-4">
								<strong>{{ $n }}</strong> 
							</div>
							<div class="col-md-8">
								{{ $k }}
							</div>
						 </div>
				     @endforeach
				 @endforeach
				 
				 
				 <h4>Phones</h4>

				 @foreach($person->otherPhones->phone as $p)
				 
				     <h5 class="subheading">Record</h5>

				     @foreach($p as $n=>$k)
				     
				         @if(!is_object($k))
				         <div class="line">
							<div class="col-md-4">
								<strong>{{ $n }}</strong> 
							</div>
							<div class="col-md-8">
								{{ $k }}
							</div>
						 </div>
						 @endif
						 
				     @endforeach
				 @endforeach

				 <h4>Miscellaneous</h4>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Emails:</strong> 
					</div>
					<div class="col-md-8">
						@php
						  $mails = "";
						  
						  foreach($person->emails as $email){
						  	$mails .= $email . ", ";
						  }
						  
						  echo rtrim($mails, ", ");
						  
						@endphp
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Deceased</strong> 
					</div>
					<div class="col-md-8">
						{{ $person->deceased }}
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Driver's Licenses</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Sex Offender</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Bankruptcies</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Judgements</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Liens</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Occupation</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				 
				 <div class="line">
					<div class="col-md-4">
						<strong>Employer</strong> 
					</div>
					<div class="col-md-8">
						&nbsp;
					</div>
				 </div>
				
			</div>
		</div>
	</div>
</div>
@endif