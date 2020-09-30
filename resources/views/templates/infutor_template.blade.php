<?php

  $r = json_decode(json_decode(decrypt($report->report)));
  
  Log::info("Report id = " . $report->id);
  
  //$r = App\Models\Infutor::standardize($report);
  
  $s = print_r($r, true);
  Log::info($s);
  
  
  if(isset($r->Detail->__type)){
  	unset($r->Detail->__type);
  }

   
  Log::info("Display the Infutor");
  //Log::info(gettype($r));

  //$s = print_r($r->Detail->IDAttributes->PropertyAttributes, true);
  //Log::info($s);
  
  
?>

@if(!$r->ResponseMsg == "Successful")
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
				
				<h4>Scores</h4>
				@foreach($r->Detail->IDScores as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				

				<h4>Property Attributes</h4>
				
				<h5 class="subheading">Additional Information</h5>
				@foreach($r->Detail->IDAttributes->PropertyAttributes->AdditionalInformation as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Attributes</h5>
				@foreach($r->Detail->IDAttributes->PropertyAttributes->Attributes as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Assessor Deed</h5>
				@foreach($r->Detail->IDAttributes->PropertyAttributes->AssessorDeed as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Mortgage</h5>
				@foreach($r->Detail->IDAttributes->PropertyAttributes->Mortgage as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h4>Demographics</h4>
				
				@foreach($r->Detail->IDAttributes->DemographicAttributes->Demographics as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				
				@foreach($r->Detail->IDAttributes->DemographicAttributes->PowerScore as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				
				<h4>Phone</h4>
				@foreach($r->Detail->IDAttributes->PhoneAttributes as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				
				@foreach($r->Detail->IDAttributes->PhoneAttributes as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h4>Automobiles</h4>
				
				<h5 class="subheading">Vehicle 1</h5>
				@foreach($r->Detail->IDAttributes->AutoAttributes->Vehicle as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Vehicle 2</h5>
				@foreach($r->Detail->IDAttributes->AutoAttributes->Vehicle2 as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Vehicle 3</h5>
				@foreach($r->Detail->IDAttributes->AutoAttributes->Vehicle3 as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h5 class="subheading">Vehicle 4</h5>
				@foreach($r->Detail->IDAttributes->AutoAttributes->Vehicle4 as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
				<h4>IP Attributes</h4>
				@foreach($r->Detail->IDAttributes->IPAttributes as $key=>$value)
				<div class="line">
					<div class="col-md-4">
						<strong>{{ $key }}</strong> 
					</div>
					<div class="col-md-8">
						{{ $value }}
					</div>
				</div>
				@endforeach
				
			</div>
		</div>
	</div>
</div>



@endif ((-- END OF RESULTS --))
