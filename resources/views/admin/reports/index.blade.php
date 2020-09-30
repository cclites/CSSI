@extends('layouts.app', [
	'title' => 'Admin Dashboard',
	'active_menu_item' => 'admin_dashboard'
])


@php

$stats = \App\Models\Stat::orderBy("description", 'asc')->get();
$types = \App\Models\Type::all();

$companies = DB::table("users")
		                 ->distinct('company_name')
		                 ->where('company_rep', true)
		                 ->orderBy('company_name', 'ASC')->get();

@endphp


@section('content')

<style>
	#this_day_average,
	#this_month_average,
	#prior_month_average,
	#ytd_average{
		font-size: 12px;
	}
	
	table.check-types{
		/*display: none;*/
		width: 100%;
	}
	
	.baseModalContent tr{
		cursor: pointer;
		height: 30px;
		border-top: 1px solid #eee;
	}
	
	.indicator_success{
		background: green;
	}
	
	.indicator_warning{
		background: yellow;
	}
	
	.indicator-danger{
		background: red;
	}
	
	.indicator{
		height: 32px;
		width: 32px;
		border-radius: 50%;
		margin: 0 auto;
		color: #fff;
		padding-top: 5px;
	}
	
	.checks-for-day,
	.checks-for-month,
	.checks-for-prior-month,
	.checks-for-ytd{
		/*display: none;*/
	}
		
</style>

<div class="check_indicators">
	<!--h4>Service Provider Status:</h4-->
	@foreach($types as $type)
	  @if($type->enabled)
	    <span class="green_indicator label-success" title="{{ $type->title }} is enabled.">&nbsp;</span>
	  @else
	    <span class="red_indicator label-danger" title="{{ $type->title }} is disabled.">&nbsp;</span>
	  @endif
	@endforeach
</div>

<h2 class="text-center">Revenue</h2>
<br>

<div class="row admin-reports-index">
	<!--div class="col-md-4 col-md-offset-4 checks-for-day"></div>
	<div class="col-md-4 col-md-offset-4 checks-for-month"></div>
	<div class="col-md-4 col-md-offset-4 checks-for-prior-month"></div>	
	<div class="col-md-4 col-md-offset-4 checks-for-ytd"></div-->
	
	<div class="col-md-3 checks-for-day"></div>
	<div class="col-md-3 checks-for-month"></div>
	<div class="col-md-3 checks-for-prior-month"></div>	
	<div class="col-md-3 checks-for-ytd"></div>
	
	<!-- **************************************************************************************************************************************** !-->
	
</div>

{{-- !! Form::open([ 'url' => secure_url('admin/checks'), 'method' => 'get'  ]) !! --}}


<div class="panel panel-default">
	<h2 class="text-center">Reports</h2>
	<br>
	<div class="panel-body">
		<input type="hidden" name="search" id="search" value="true">
		<input type="hidden" class="form-control date-selection" name="before" id="end_date">
		<input type="hidden" class="form-control date-selection" name="after" id="start_date" value="{{ date("Y-m-d") }}">
		<button type="button" class="btn btn-primary btn-lg btn-block admin-check-search" onclick="Admin.getCustomReportData('adminDashboard')">Daily Totals</button>
	</div>
	<br>
</div>

{{-- !! Form::close() !! --}}

<br>
<h2 class="text-center">Status Monitors</h2>
<br>
<article class="row">
	
	<div class="panel-group">
	
		@foreach($stats as $stat)
		<div class="panel panel-info col-md-2" style="height:155px; margin: 6px;">
			<div class="panel-body text-center">
				<p>{{ $stat->description }}</p>
				<p>
					@if($stat->description == "Mvr Test Password Days Remaining" || $stat->description == "Mvr Password Days Remaining")
					
					  @if($stat->val < 5)
					    <div class="indicator indicator_danger">{{ $stat->val }}</div>
					  @elseif($stat->val >=5 && $stat->val <= 10)
					    <div class="indicator indicator_warning">{{ $stat->val }}</div>
					  @else
					    <div class="indicator indicator_success">{{ $stat->val }}</div>
					  @endif
					   
					@else
					  <div class="indicator indicator_success"></div>
					@endif
				</p>
				<p>{{ displayDateTime($stat->updated_at) }}</p>
			</div>
		</div>
		@endforeach
				
	</div>		
		
</article>

<div class="baseModal" onclick="closeModal();">
	<div class="baseModalContent" id="baseModalContent"></div>
</div>

<template style="display: none;" id="todays_revenue">
	<div class="panel panel-default">
	    <div class="panel-body text-center">
	    	<h3>For Day</h3>
	    	<table class="totals_table col-sm-12">
	    		<thead>
	    			<tr>
	    				<td>Revenue</td>
	    				<td>Orders</td>
	    				<td>Checks</td>
	    				<td>Completed</td>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<tr>
	    				<td><span class="text-info this_day_revenue"></span></td>
	    				<td><span class="text-info this_day_total_orders"></span></td>
	    				<td><span class="text-info this_day_total_checks"></span></td>
	    				<td><span class="text-info this_day_total_completed"></span></td>
	    			</tr>
	    		</tbody>
	    	</table>

	    	<p class="text-muted" id="this_day_average">
	    	</p>
	    	
	    	<div class="check_type_modal_content">
	    		<canvas class="checks-for-day check-types" id="checks-for-day"  start="{{ \Carbon\Carbon::now()->startOfDay() }}" end=""></canvas>
	    	</div>
	    </div>
	</div>
</template>

<template style="display: none;" id="month_revenue">
	<div class="panel panel-default">
	    <div class="panel-body text-center">
	    	
	    	<h3>For Month</h3>
	    	<table class="totals_table col-sm-12">
	    		<thead>
	    			<tr>
	    				<td>Revenue</td>
	    				<td>Orders</td>
	    				<td>Checks</td>
	    				<td>Completed</td>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<tr>
	    				<td><span class="text-info this_month_total_revenue"></span></td>
	    				<td><span class="text-info this_month_total_orders"></span></td>
	    				<td><span class="text-info this_month_total_checks"></span></td>
	    				<td><span class="text-info this_month_total_completed"></span></td>
	    			</tr>
	    		</tbody>
	    	</table>

	    	
	    	<p class="text-muted" id="this_month_average">
	    	</p>
	    	
	    	<div class="check_type_modal_content">
	    		<canvas class="checks-for-month check-types" id="checks-for-month" start="{{ \Carbon\Carbon::now()->startOfMonth()->startOfDay() }}" end="{{ \Carbon\Carbon::now()->subDay()->endOfDay() }}"></canvas>
	    	</div>
	    </div>
	</div>
</template>

<template style="display: none;" id="prior_month_revenue">
	<div class="panel panel-default">
	    <div class="panel-body text-center">
	    	
	    	<h3>For Prior Month</h3>
	    	<table class="totals_table col-sm-12">
	    		<thead>
	    			<tr>
	    				<td>Revenue</td>
	    				<td>Orders</td>
	    				<td>Checks</td>
	    				<td>Completed</td>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<tr>
	    				<td><span class="text-info prior_month_total_revenue"></span></td>
	    				<td><span class="text-info prior_month_total_orders"></span></td>
	    				<td><span class="text-info prior_month_total_checks"></span></td>
	    				<td><span class="text-info prior_month_total_completed"></span></td>
	    			</tr>
	    		</tbody>
	    	</table>
	    	
	    	<p class="text-muted" id="prior_month_average">
	    	</p>
	
	    	<!-- h5 class="table-toggle" data-type="cpm"><i class="fa fa-folder-open" aria-hidden="true"></i></h5 -->
	    	<div class="check_type_modal_content">
	    		<canvas class="checks-prior-month check-types" id="checks-prior-month" start="{{ \Carbon\Carbon::today()->startOfMonth()->subMonth()->startOfDay() }}" end="{{ \Carbon\Carbon::today()->subMonth()->endOfMonth()->endOfDay() }}"></canvas>
	    	</div>
	    	
	    </div>
	</div>
</template>

<template style="display: none;" id="ytd_revenue">
	<div class="panel panel-default">
	    <div class="panel-body text-center">
	    	
	    	
	    	<h3>For Year</h3>
	    	<table class="totals_table col-sm-12">
	    		<thead>
	    			<tr>
	    				<td>Revenue</td>
	    				<td>Orders</td>
	    				<td>Checks</td>
	    				<td>Completed</td>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<tr>
	    				<td><span class="text-info ytd_revenue"></span></td>
	    				<td><span class="text-info ytd_total_orders"></span></td>
	    				<td><span class="text-info ytd_total_checks"></span></td>
	    				<td><span class="text-info ytd_total_completed"></span></td>
	    			</tr>
	    		</tbody>
	    	</table>
	    		    	
	    	<p class="text-muted" id="ytd_average">
	    	</p>
	
	    	<div class="check_type_modal_content">
	    		<canvas class="checks-for-year check-types" id="checks-for-year" start="{{ \Carbon\Carbon::now()->startOfYear()->startOfDay() }}" end="{{ \Carbon\Carbon::now()->subMonth()->endOfMonth()->endOfDay() }}"></canvas>
	    	</div>
	    	
	    </div>
	</div>
</template>

<template id="chart_canvas_template">
	<canvas id="chart_canvas" width="400" height="400"></canvas>
</template>

<script>
	
	function populateChecksByDay(data){
		
		console.log("Checks for day");

    	$(".checks-for-day").html( $("#todays_revenue").html() );
		$(".this_day_revenue").html( "$" + data.totalAmount.toFixed(2) );
		$(".this_day_total_checks").html( data.totals.count );
		$(".this_day_total_orders").html( data.orderCount );
		$(".this_day_total_completed").html( data.completedChecksCount );
		
		if(data.totals.average){
			$("#this_day_average").html( "(" + data.totals.average.toFixed(2) + ")");
		}else{
			data.totals.average = 0;
		}

		buildBarChart(data.counts, 'checks-for-day');
		$(".checks-for-day").show(400);
		
		console.log("Show Checks for Month");
		checksForMonth();

	}
	
	function populateChecksforMonth(data){
		
		console.log("Checks for month");

		$(".checks-for-month").html( $("#month_revenue").html() );
  
		$(".this_month_total_revenue").html( "$" + data.totalAmount.toFixed(2) );
		$(".this_month_total_checks").html(data.totals.count);
		$(".this_month_total_orders").html( data.orderCount );
		$(".this_month_total_completed").html( data.completedChecksCount );
		
		$("#this_month_average").html( "(" + data.totals.average.toFixed(2) + ")");
		
		buildBarChart(data.counts, 'checks-for-month');
		
		$(".checks-for-month").show(400);
		
		checksForPriorMonth();
	}
	
	function populateChecksforPriorMonth(data){
		
		console.log("Checks for prior month");

		$(".checks-for-prior-month").html( $("#prior_month_revenue").html() );
  
		$(".prior_month_total_revenue").html( "$" + data.totalAmount.toFixed(2) );
		$(".prior_month_total_checks").html(data.totals.count);
		$(".prior_month_total_orders").html( data.orderCount );
		$(".prior_month_total_completed").html( data.completedChecksCount );
		
		
		$("#prior_month_average").html( "(" + data.totals.average.toFixed(2) + ")");

		buildBarChart(data.counts, 'checks-prior-month');
		checksForYtd();
	}
	
	function populateChecksByYtd(data){
		
		console.log("Checks for YTD");

		$(".checks-for-ytd").html( $("#ytd_revenue").html() );
  
		$(".ytd_revenue").html( "$" + data.totalAmount.toFixed(2) );
		$(".ytd_total_checks").html(data.totals.count);
		$(".ytd_total_orders").html( data.orderCount );
		$(".ytd_total_completed").html( data.completedChecksCount );
		
		$("#ytd_average").html( "(" + data.totals.average.toFixed(2) + ")");

		buildBarChart(data.counts, 'checks-for-year');
		initTableListeners();
	}
	
	function checksForDay(){
		
		$(".loading-spinner").show(400);
		
		 const data = {
				company_id : "{{ Auth::user()->company_id  }}",
				_token: $('meta[name="csrf-token"]').attr('content')
			},
			url = "/admin/reports/checksForDay",
			type = "GET",
			success = function(data){

				populateChecksByDay(data);
				$(".loading-spinner").hide(400);

			},
			failure = function(a,b,c){
				
				$(".loading-spinner").hide(400);
				$(".stats-error").show(400);
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);	
	}
	
	function checksForMonth(){
		
		$(".loading-spinner").show(400);
		
		const data = {
				company_id : "{{ Auth::user()->company_id  }}",
				_token: $('meta[name="csrf-token"]').attr('content')
			},
			url = "/admin/reports/checksForMonth",
			type = "GET",
			success = function(data){

				populateChecksforMonth(data);
				$(".loading-spinner").hide(400);

			},
			failure = function(a,b,c){
				
				$(".loading-spinner").hide(400);
				$(".stats-error").show(400);
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);
	}
	
	function checksForPriorMonth(){

		$(".loading-spinner").show(400);
		
		const data = {
				company_id : "{{ Auth::user()->company_id  }}",
				_token: $('meta[name="csrf-token"]').attr('content')
			},
			url = "/admin/reports/checksForPriorMonth",
			type = "GET",
			success = function(data){

				populateChecksforPriorMonth(data);
				$(".loading-spinner").hide(400);

			},
			failure = function(a,b,c){
				
				$(".loading-spinner").hide(400);
				$(".stats-error").show(400);
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);
	}
	
	function checksForYtd(){
		
		$(".loading-spinner").show(400);
		
		 const data = {
				company_id : "{{ Auth::user()->company_id  }}",
				_token: $('meta[name="csrf-token"]').attr('content')
			},
			url = "/admin/reports/checksForYtd",
			type = "GET",
			success = function(data){

				populateChecksByYtd(data);
				$(".loading-spinner").hide(400);

			},
			failure = function(a,b,c){
				
				$(".loading-spinner").hide(400);
				$(".stats-error").show(400);
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);	
	}
	
	
	
	function buildTable(checks, after, before){
		
		/*
		let html = "";
		
		for(var chk in checks){
			
			check = checks[chk];
			
			html += '<tr data-type="' + check.type_id + '"  data-after="' + after + '" data-before="' + before + '">' +
			        '  <td class="col-md-6 table-row-title">' + check.title + '</td>' +
			        '  <td class="col-md-6 table-row-amount">' + check.count + '</td>' +
			        '</tr>';
			        
		}
		
		return html;
		*/
		
	}
	
	function buildBarChart(checkTypes, containerId){
		
		var labels = [],
		    colors = [],
		    data = [],
		    palette = {
		    	"primary" : "#007bff",
		    	"secondary" : "#6c757d ",
		    	"info" : "#17a2b8",
		    	"danger" : "#dc3545",
		    	"warning" : "#ffc107",
		    	"success" : "#28a745",
		    	"SlateBlue" : "SlateBlue"
		    }
		    
		for (var index in checkTypes){
			
			if(checkTypes[index].count > 0){
				console.log(checkTypes[index]);
				labels.push(checkTypes[index].title);
				data.push(checkTypes[index].count);
				colors.push(palette[checkTypes[index].color]);
			}
				
		}
		
		//console.log(colors);
        /*
		for (var check in checkTypes){
			labels.push(checkTypes[check].title);
			data.push(checkTypes[check].count);
			colors.push(palette[checkTypes[check].color]);
		}
		*/
		
	
		new Chart(document.getElementById(containerId), {
		    type: 'bar',
		    data: {
		      labels: labels,
		      //labels: false,
		      datasets: [
		        {
		          label: "Check Types",
		          backgroundColor: colors,
		          data: data
		        }
		      ]
		    },
		    options: {
		      legend: { display: false },
		      height: 500,
		      onClick: graphClickEvent,
		      tooltips: {
		      	intersect: false
		      },
		      title: {
		        display: false,
		      },
		      scales: {
		        yAxes: [{
		            ticks: {
		                beginAtZero: true
		            },
		        }],
		        xAxes: [{
		        	ticks: {
		        		//autoSkip: false
		        		display: false
		        	}
		        }]
		      }
		    },
		    scaleLabel: {
		    	display: false
		    },
		});
		
		//$(".baseModal").addClass("base_modal_display");
    }
    
    function graphClickEvent(event, array){
    	
        //.log(array);

        if(array.length == 0){
        	return;
        }
        
    	var start = event.target.attributes.start.value,
    	    end = event.target.attributes.end.value,
    	    type = typeLookup[array[0]._view.label];
    	
    	start = start.split(" ")[0];
    	end = end.split(" ")[0];
    	
    	Admin.getCheck(end, start, type, null);
    	
    }
    
    function initTableListeners(){
		
		/*
		$(".check-types tr").click(function(){
			var type = $(this).data("type"),
			    before = $(this).data("before"),
			    after = $(this).data("after"),
			    company_id = $(this).data("company") ? $(this).data("company") : null;
			Admin.getCheck(before, after, type, company_id);
		});
		*/
	}


	function initTableToggle(){
		
		/*
		$(".table-toggle").click(function(){
			
			BaseModal.remove();
			$(".check_type_modal_content").hide();
			
			BaseModal.display($(this).next().html());
			$(".baseModalContent").show();
			
			//$(this).next().show(400);
			
			//by now, the canvas should already be loaded. Just display.
			//$(".baseModal").addClass("base_modal_display");
			
			
			
			BaseModal.display($(this).next().html());
			
			$(".baseModalContent table").show();
			
			
			
			$("table").hide();
			$(this).next().show(400);
			console.log($(this).next(400));
			
			
		});
		*/
		
		//buildBarChart(data.checks);
		
	}
	
	
	
	function formatCurrency(amount){
		
		return Number(amount).toLocaleString('en-US', {style: 'currency', currency: 'USD'});
		
	}
	
	function closeModal(){
		BaseModal.remove();	
	}
	
	document.addEventListener("DOMContentLoaded", function(event) { 
	  checksForDay();
	  
	  /*
	  updateRevenue = setInterval(function(){
	  	checksForDay();
	  }, 12000);
	  */
	});
	
	@php
	
	  $map = [];
	  $types = Cache::get('types')->toArray();
	  
	  foreach($types as $type){
	  	$map[$type["title"]] = $type["id"];
	  }
	  
	  
	@endphp
	
	
	var typeLookup = {!! json_encode(  $map ) !!},
	    updateRevenue = '';
	
	
</script>

@endsection