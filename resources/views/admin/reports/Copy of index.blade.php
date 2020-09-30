@extends('layouts.app', [
	'title' => 'Admin Dashboard',
	'active_menu_item' => 'admin_dashboard'
])


@php

//$s = print_r($data, true);
//Log::info($s);
//return;
@endphp


@section('content')

<div class="row admin-reports-index">
	
	<div class="col-md-4">
		
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<p class="text-muted">
		    		Today's Revenue
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ displayMoney($data['this_day_revenue']) }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	<p class="text-muted">
		    		Total Checks
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ $data['this_day_total_checks'] }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Paid Checks By Type
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table class="checks-by-day">
		    		@foreach( $data['check_types'] as $type)
		    		 <tr data-type="{{ $type->id }}"  data-after="{{ Carbon\Carbon::today()->toDateString() }}" data-before="">
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['this_day_live_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	

		    	{{-- 
		    	<p class="text-muted">
		    		Test Checks By Type ({{ $data['this_day_total_test_checks'] }})
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table>
		    		@foreach( $data['check_types'] as $type)
		    		 <tr>
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['this_day_test_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	--}}
		    	
		    </div>
		</div>
	</div>
		
	{{-- 
    <div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<p class="text-muted">
		    		This Month's Revenue
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ displayMoney($data['this_month_revenue']) }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Total Checks
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ $data['this_month_total_checks'] }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Paid Checks By Type
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table class="checks-this-month">
		    		@foreach( $data['check_types'] as $type)
		    		 <tr data-type="{{ $type->id }}"  data-after="{{ Carbon\Carbon::now()->startOfMonth()->toDateString() }}" data-before="{{ Carbon\Carbon::today()->toDateString() }}">
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['this_month_live_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	
		    	{{-- 
		    	<p class="text-muted">
		    		Test Checks By Type ({{ $data['this_month_total_test_checks'] }})
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table>
		    		@foreach( $data['check_types'] as $type)
		    		 <tr>
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['this_month_test_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	--}}
		    	
		    </div>
		</div>
		
	</div>
	--}}
	
	{{-- 	
    <div class="col-md-4">
		
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<p class="text-muted">
		    		Last Month's Revenue
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ displayMoney($data['last_month_revenue']) }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Total Checks
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ $data['last_month_total_checks'] }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Paid Checks By Type
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table class="checks-last-month">
		    		@foreach( $data['check_types'] as $type)
		    		 <tr data-type="{{ $type->id }}"  data-after="{{ Carbon\Carbon::now()->startOfMonth()->subMonth()->toDateString() }}" data-before="{{ Carbon\Carbon::now()->startOfMonth()->toDateString() }}">
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['last_month_live_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	
		    	
		    	{{-- 
		    	<p class="text-muted">
		    		Test Checks By Type ({{ $data['last_month_total_test_checks'] }})
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table>
		    		@foreach( $data['check_types'] as $type)
		    		 <tr>
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['last_month_test_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	--}}
		    	
		    </div>
		</div>
		
	</div>
	--}}
		
	{{-- 
    <div class="col-md-4">

		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<p class="text-muted">
		    		YTD Revenue
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ displayMoney($data['ytd_revenue']) }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	<p class="text-muted">
		    		Total Checks
		    	</p>
		    	<h1 class="text-large">
		    		<strong>
		    			<span class="text-green">
		    				{{ $data['ytd_total_checks'] }}
		    			</span>
		    		</strong>
		    	</h1>
		    	
		    	
		    	<p class="text-muted">
		    		Paid Checks By Type
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table class="checks-ytd">
		    		@foreach( $data['check_types'] as $type)
		    		 <tr data-type="{{ $type->id }}"  data-after="{{ Carbon\Carbon::now()->subYear()->toDateString() }}" data-before="{{ Carbon\Carbon::now()->toDateString() }}"> 
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['ytd_live_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	
		    	{{--
		    	<p class="text-muted">
		    		Test Checks By Type ({{ $data['ytd_total_test_checks'] }})
		    	</p>
		    	<h5 class="table-toggle"><i class="fa fa-folder-open" aria-hidden="true"></i></h5>
		    	<table>
		    		@foreach( $data['check_types'] as $type)
		    		 <tr>
		    		 	<td class="col-md-3 table-row-title">
		    		 		{{ $type->title }}
		    		 	</td>
		    		 	<td class="col-md-1 table-row-amount">
		    		 		{{ $data['ytd_test_checks'][$type->id] }}
		    		 	</td>
		    		 </tr>
		    		
		    		@endforeach
		    	</table>
		    	--}}
		    	
		    </div>
		</div>
	  </div>
      --}}
      
	</div>

    {{-- 
	<div class="col-md-8">
		
		<div class="row">
			<div class="col-sm-4">
				<div class="panel panel-default">
				    <div class="panel-body text-center">
				    	<h1 class="text-large">
				    		<strong>
				    			<span class="text-primary">
				    				{{ $data['user_count'] }}
				    			</span>
				    		</strong>
				    	</h1>
				    	<p class="text-muted">
				    		Total Users
				    	</p>
				    </div>
				</div>
			</div>
			
		</div> <!-- /.row -->

	</div>
	--}}
</div>

<script>
	
	
	
	
</script>

@endsection