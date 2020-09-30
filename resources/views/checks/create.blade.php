@extends('layouts.app', [
	'title' => 'Run a Check',
	'active_menu_item' => 'create_check'
])

@section('content')

@php
  $types = \App\Models\Type::all();
@endphp

@foreach ($types->chunk(4) as $row)
	<div class="row">
		@foreach ($row as $type)
		
			@if($type->enabled)
			<div class="col-md-3 col-xs-6">
				<div class="panel panel-default panel-select" data-type="{{ $type->id }}">
					<div class="panel-body text-center">
						<h1 class="text-large">
							<span class="text-{{ $type->color }}">
				    			<i class="fa {{ $type->icon }}" aria-hidden="true"></i>
				    		</span>
				    	</h1>
				    	<p class="lead">
				    		{!! $type->two_line_title !!}
				    	</p>
					</div>
				</div>
			</div>
			@else
			<div class="col-md-3 col-xs-6 disabled-type" data-type="{{ $type->id }}">
				<div class="panel panel-default" style="height: 195px;">
					<div class="panel-body text-center">
						<h1 class="text-large">
							<span class="text-{{ $type->color }}">
				    			<!--i class="fa {{ $type->icon }}" aria-hidden="true"></i-->
				    		</span>
				    	</h1>
				    	<p class="lead">
				    		{!! $type->two_line_title !!} <br>is currently unavailable
				    	</p>
					</div>
				</div>
			</div>
			@endif
			
		@endforeach
	</div>
@endforeach

<style>
	div[data-type="11"],
	div[data-type="12"],
	div[data-type="13"],
	div.disabled-type{
		display: none;
	}
</style>


<div class="text-center">
	<button class="btn btn-primary btn-lg btn-submit">&nbsp;&nbsp;&nbsp;Continue&nbsp;&nbsp;&nbsp;</button>
</div>
	
@endsection


@section('js')
<script>
$(function() {
	var types = [];

	$( ".panel-select" ).each(function() {
		types.push({
			"id": $(this).data("type"),
			"icon": $(this).find( ".text-large" ).html(),
			"selected": false
		});
	});


	$(".panel-select").click(function(e) {
	    for(var i = 0; i < types.length; i++) {
	        if (types[i].id == $(this).data("type") && types[i].selected == false) {
	        	console.log("selected: "+types[i].id)
	            types[i].selected = true;
	            $(this).css("background-color", "#337ab7");
	            $(this).css("color", "#FFFFFF");
	            $(this).find( ".text-large" ).html('<i class="fa fa-check-circle" aria-hidden="true"></i>');
	        }
	        else if (types[i].id == $(this).data("type") && types[i].selected == true) {
	        	console.log("unselected: "+types[i].id)
	            types[i].selected = false;
	            $(this).css("background-color", "");
	            $(this).css("color", "");
	            $(this).find( ".text-large" ).html(types[i].icon);
	        }
	    }
	});

	$(".btn-submit").click(function(e) {
		var url = "{{ secure_url('checks/input').'?' }}";
		for(var i = 0; i < types.length; i++) {
			if (types[i].selected == true) {
				url = url + '&types[]=' + types[i].id;
			}
		}

		window.location.href = url;
	});
});
</script>
@endsection