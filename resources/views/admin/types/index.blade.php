<?php

	use App\Models\Type;

	$types = Type::all();
?>

@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-4">
    	
    	<table>
    		<thead>
    			<tr>
    				<th>Type</th>
    				<th>Enabled</th>
    			</tr>
    		</thead>
    		<tbody>
    			
		    	@foreach($types as $type)
		    		<tr>
		    			<td>{{ $type->title }}</td>
		    			
		    			@if($type->enabled)
		    				<td style="text-align: center;"><input type="checkbox" onclick="Admin.toggleCheckEnable({{ $type->id }});" checked></td>
		    			@else
		    				<td style="text-align: center;"><input type="checkbox" onclick="Admin.toggleCheckEnable({{ $type->id }});"></td>
		    			@endif
		    		</tr>
		    	
		    	@endforeach
	    	
	    	</tbody>
    	</table>
    </div>
</div>


@endsection