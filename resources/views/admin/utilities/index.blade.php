@extends('layouts.app', [
    'title' => 'Tools',
    'active_menu_parent' => 'admin',
    'active_menu_item' => 'admin_tools'
])

@section('content')

@php
  //var_dump(\Carbon\Carbon::createFromFormat("U", \Carbon\Carbon::now('UTC')->format("U"))->format("Y-m-d H:i:s") == \Carbon\Carbon::now('UTC')->format("Y-m-d H:i:s"));
@endphp

<style>
</style>

<h1>Utilities</h1>

<div class="row">
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-table" aria-hidden="true"></i>
            </h1>
			
			<p>
	            <a onclick="Admin.getCustomReport('invoice_export');" class="btn btn-primary">
	                Invoice Report
	            </a>
	            
	            <a onclick="Admin.getCustomReport('dailies');" class="btn btn-primary">
	                Daily Invoice Report
	            </a>
	            
	            <a onclick="Admin.getRawTotals();" class="btn btn-primary">
	                Raw Totals for Month
	            </a>
	            
            </p>
		</div>
	</div>
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-building" aria-hidden="true"></i>
            </h1>
            
			<p>
	            <a onclick="Admin.getCompanies();" class="btn btn-primary">
	                Export Companies
	            </a>
            </p>
		</div>
	</div>
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-address-book" aria-hidden="true"></i>
            </h1>
            
			<p>
	            <a onclick="Admin.getUsers();" class="btn btn-primary">
	                Export Users
	            </a>
            </p>
		</div>
	</div>
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-usd" aria-hidden="true"></i>
            </h1>
            
			<p>
	            <a href="{{ secure_url('admin/transactions/edit') }}" class="btn btn-primary">
	                Edit Transactions
	            </a>
            </p>
		</div>
	</div>
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<h1 class="text-large">
                <i class="fa fa-paperclip" aria-hidden="true"></i>
            </h1>
            
            <div class="row">
            	<label style="display: inline-block; width: 100px; text-align: right; margin-right: 6px;">Start Date:</label><input style="width: 120px;" type="date" id="start_date" placeholder="mm/dd/yyyy">
            </div>
            
			<div class="row">
				<label style="display: inline-block; width: 100px; text-align: right; margin-right: 6px;">End Date:</label><input style="width: 120px;" type="date" id="end_date" placeholder="mm/dd/yyyy">
			</div>

            <br>
			<p>
	            <a onclick="Admin.getCustomReport('custom');" class="btn btn-primary">
	                Custom Report
	            </a>
            </p>
		</div>
	</div>
	
	<div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-tasks" aria-hidden="true"></i>
            </h1>
            
			<p>
	            <a href="{{ secure_url('admin/types') }}" class="btn btn-primary">
	                Manage Types
	            </a>
            </p>
		</div>
	</div>
	
	<!--div class="col-md-3 tool-tile skin-black">
		<div class="panel panel-default text-center">
			
			<div class="spacer"></div>
			
			<h1 class="text-large">
                <i class="fa fa-file" aria-hidden="true"></i>
            </h1>
            
			<p>
	            <a href="{{ secure_url('/admin/invoices/pdf') }}" class="btn btn-primary">
	                Invoices
	            </a>
            </p>
		</div>
	</div-->
	
	
	<!--div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large text-primary">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Refresh
                </p>
		        <p>
			        <a href="{{ secure_url('admin/utilities/session') }}" class="btn btn-lg btn-primary">
			            Flush Session Data
			        </a>
			    </p>
			    <p>
			        <a href="{{ secure_url('admin/utilities/cache') }}" class="btn btn-lg btn-danger">
			            Flush Cache Data
			        </a>
			    </p>
		    </div>
		</div>
	</div-->

	<!--div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large text-yellow">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Emails
                </p>
		        <p>
			        <a href="{{ secure_url('admin/utilities/emails/employment') }}" class="btn btn-lg btn-warning">
			            Employment Check
			        </a>
			    </p>
			    
		    </div>
		</div>
	</div-->

	<!--div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-body text-center">
		    	<h1 class="text-large text-info">
                    <i class="fa fa-sitemap" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Host & Proxy
                </p>
		        <p class="text-muted">
		        	Client IP: {{ Request::ip() }}
			    </p>
			    <p class="text-muted">
		        	URL Root: {{ secure_url('/') }}
			    </p>
			    
			    {{-- 
			    <p class="text-muted">
		        	HTTP Host: {{ request()->getHttpHost() }}
			    </p>
			    --}}
			    <p>
			    	<a href="{{ secure_url('admin/utilities/kint') }}" class="btn btn-lg btn-info">
			    		Kint Request
			    	</a>
			    </p>
		    </div>
		</div>
	</div-->
</div>

@endsection