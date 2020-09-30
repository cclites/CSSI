<?php

use App\Models\State;
use App\Models\Type;
use App\Models\County;

$states = App\Models\State::all();
$types = App\Models\Type::all();
$counties = App\Models\County::all();

?>

@extends('layouts.app', [
	'title' => 'Price Management',
	'active_menu_item' => 'admin_statePricing'
])

@section('content')

     <div class="col-md-10 price-panel">
		<div class="panel panel-danger">
			<div class="panel-body">
				<h4>Base Prices</h4>
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
				<table class="table table-condensed pricing_table">
					
					@foreach($types as $type)
					 <tr>
					 	
					 	<td>
					 		<strong>
					 			{{ $type->title }}
					 		</strong>
					 	</td>
					 	
					 	<td>
					 		<input onchange="Admin.updateBaseCheckPrice(this, {{ $type->id }});" type='text' step='0.01' value='{{ number_format($type->default_price, 2) }}'>
					 	</td>
					 	
					 </tr>
					@endforeach
				</table>
			</div>
			
		</div>
	</div>

	<div class="col-md-10 price-panel">
		<div class="panel panel-danger">
			<div class="panel-body">
				<h4>County Prices</h4>
				
				<div class="form-group">
                    {!! Form::label('state_select', 'State Select') !!}
                    {!! Form::select('state_select', cache('states')->pluck('title', 'code'), null, ['class' => 'form-control', 'placeholder' => '- Select a State -', 'data-live-search' => 'true', 'id' => 'state_select']) !!}
                </div>
               <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
               <table class='pricing_table' id="county_pricing">
               	
               </table>

			</div>
			
		</div>
	</div>
	
	<div class="col-md-10 price-panel">
		<div class="panel panel-danger">
			<div class="panel-body">
				<h4>State Prices</h4>
				<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
				<table class="table table-condensed pricing_table">
					<thead>
					<tr>
						<th></th>
						<th>
							<strong>Pass Through</strong>
						</th>
						<th>
							<strong>MVR</strong>
						</th>
					</tr>
					</thead>
					<tbody>
					@foreach($states as $state)
					 <tr>
					 	<td>
					 		<strong>
					 			{{ $state->title }}
					 		</strong>
					 	</td>
					 	
					 	<td>
					 		<input onchange="Admin.updateStateExtra(this, {{ $state->id }});" type='text' value='{{ number_format($state->extra_cost, 2) }}'>
					 	</td>
					 	
					 	<td>
					 		<input onchange="Admin.updateStatePrice(this, {{ $state->id }});" type='text' value='{{ number_format($state->mvr_cost, 2) }}'>
					 	</td>
					 	
					 </tr>
					@endforeach
					</tbody>
				</table>
			</div>
			
		</div>
	</div>
	
	

@endSection

@section('js')
<script>
$(function() {

    $("#state_select").change(function(e) {
        $.get( "{{ secure_url('api/counties') }}?state_code="+$(this).val(), function( response ) {
            //console.log(response);
            $('#county_pricing').empty();

            $.each(response.data, function(key, county) {
            	
            	var html = "<tr><td>" + county.title + "</td><td><input type='text' value='" + county.extra_cost +"' onchange='Admin.updateCountyExtra(this, " + county.id + ");'></td>";
            	
                $('#county_pricing')
                    .append(html);
            	});
            

            $('#state_select').selectpicker('refresh');
        });
        
        $('#county_pricing').show();
    });
    
    $(".glyphicon-arrow-down").click(function(){
    	$(this).next("table").toggle(600);
    });

});
</script>
@endsection