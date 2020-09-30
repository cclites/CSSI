@extends('layouts.app', [
'title' => $user->full_name,
'active_menu_item' => 'admin_users'
])

@section('content')

@php
$types = App\Models\Type::all();
$adj = 0;

$adjustment = DB::table("adjustments")->where('company_id',$user->company_id)->where("amount", ">", 0)->get();

if($adjustment){
$adj = $adjustment->sum("amount");
}

$min = 0;

$minimum = DB::table("minimums")->where('company_id',$user->company_id)->first();
if($minimum){
$min = $minimum->amount;
}

@endphp

<h2>{{ $user->company_name }}</h2>

<div class="row">
	<!--div class="col-md-12"-->

	<h4 class="loading-spinner text-center"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
	<br>
	<br>
	<span>Retrieving Check Data</span></h4>

	<div class="panel-body check-data panel-primary">
		<h4 class="stats-error text-center" style="display:none;">Stats are unavailable</h4>

		<div class="stats" style="display:none;">
			<h3 class="text-center">Stats</h3>
			<div class="stats-container">

				<div class="row">

					<div class="col-md-3">
						<div class="panel panel-danger">
							<div class="panel-body">
								<h5 class="text-center">Checks For Day</h5>

								<div class="col-sm-3">
									<strong> # Checks </strong>
								</div>
								<div class="checks_count_for_day col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Amount </strong>
								</div>
								<div class="amount_for_day col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Types </strong>
								</div>
								<div class="types_for_day col-sm-9"></div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="panel panel-danger">
							<div class="panel-body">
								<h5 class="text-center">Month to Date</h5>

								<div class="col-sm-3">
									<strong> # Checks </strong>
								</div>
								<div class="checks_count_for_month col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Amount </strong>
								</div>
								<div class="amount_for_month col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Types </strong>
								</div>
								<div class="types_for_month col-sm-9"></div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="panel panel-danger">
							<div class="panel-body">
								<h5 class="text-center">Previous Month</h5>

								<div class="col-sm-3">
									<strong> # Checks </strong>
								</div>
								<div class="checks_count_for_previous_month col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Amount </strong>
								</div>
								<div class="amount_for_previous_month col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Types </strong>
								</div>
								<div class="types_for_previous_month col-sm-9"></div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="panel panel-danger">
							<div class="panel-body">
								<h5 class="text-center">Year to Date</h5>

								<div class="col-sm-3">
									<strong> # Checks </strong>
								</div>
								<div class="checks_count_for_ytd col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Amount </strong>
								</div>
								<div class="amount_for_ytd col-sm-9"></div>

								<div class="col-sm-3">
									<strong> Types </strong>
								</div>
								<div class="types_for_ytd col-sm-9"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /div -->
</div>

<!-- ***************************************************  -->

<div class="tab-bar border-bottom">

	<button onclick="Admin.toggleTabContent(this, 'user_info')" class="btn btn-lg btn-primary">
		User Information
	</button>
	@if($user->company_rep)
	<button onclick="Admin.toggleTabContent(this, 'pricing_info')" class="btn btn-lg" style="display: none;">
		Pricing Info
	</button>
	@endif
	<button onclick="Admin.toggleTabContent(this, 'user_permissions')" class="btn btn-lg">
		User Permissions
	</button>

	@if($user->hasRole('limited_admin'))
	<button onclick="Admin.toggleTabContent(this, 'admin_permissions')" class="btn btn-lg">
		Limited Admin Permissions
	</button>
	@endif

	<hr>

</div>

<div class="row tab-content active" id="user_info">
	<div class="col-md-12">
		<div class="panel panel-danger">
			<div class="panel-body">
			
				<button class="btn" data-id="{{ $user->id }}" data-type="tou_reset" onkeyup="Admin.resetTou(this)">
					Reset TOU
				</button>
				
				<table class="table-condensed">
					<tr>
						<td><strong>Name:</strong></td>
						<td>{{ $user->full_name }}</td>			
					</tr>

					<tr>
						<td><strong>First Name:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="first_name" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->first_name }}">
						</td>
					</tr>

					<tr>
						<td><strong>Last Name:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="last_name" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->last_name }}">
						</td>
					</tr>

					<tr>
						<td><strong>Company:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="company_name" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->company_name }}">
						</td>
					</tr>
					<tr>
						<td><strong>Company ID:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="company_id" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->company_id }}">
						</td>
					</tr>
					<tr>
						<td><strong>Email:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="email" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->email }}" disabled>
						</td>
					</tr>
					<tr>
						<td><strong>Invoicing:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="invoice" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->invoice }}">
						</td>
					</tr>
					<tr>
						<td><strong>Phone:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="phone" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->display_phone }}">
						</td>
					</tr>
					<tr>
						<td><strong>Extension:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="extension" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->extension }}">
						</td>
					</tr>
					<tr>
						<td><strong>Cell Phone:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="cell_phone" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->cell_phone }}">
						</td>
					</tr>
					<tr>
						<td><strong>Address (Line 1):</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="address" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->address }}">
						</td>
					</tr>
					<tr>
						<td><strong>Address (Line 2):</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="secondary_address" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->secondary_address }}">
						</td>
					</tr>
					<tr>
						<td><strong>Website:</strong></td>
						<td>
						<input class="admin-company-id" data-id="{{ $user->id }}" data-type="website" onkeyup="Admin.updateCompanyId(this)" value="{{ $user->website }}">
						</td>
					</tr>
					<tr>
						<td><strong>Credit Card:</strong></td>
						<td> @if ($user->card_brand)
						{{ $user->card_brand }} ending in {{ $user->card_last_four }}
						@else
						No card on file
						@endif </td>
					</tr>
					<tr>
						<td><strong>IP Address:</strong></td>
						<td>{{ $user->ip }}</td>
					</tr>
					<tr>
						<td><strong>Signup Date:</strong></td>
						<td>{{ displayDate($user->created_at) }}</td>
					</tr>
					<tr>
						<td><strong>Last Login:</strong></td>
						<td>{{ displayDate($user->updated_at) }}</td>
					</tr>
				</table>

			</div>
		</div>
	</div>
</div>

@if($user->company_rep)
<div class="row tab-content" id="pricing_info">
	<div class="col-md-12">
		<div class="panel panel-danger">
			<div class="panel-body">

				<p>
					<table class="pricing_table user_pricing_table table-condensed">

						<tbody>

							<tr>
								<td><strong>Add adjustment</strong></td>
								<td>
								<input id="adjustmentAmount" size="8">
								</td>
								<td>
								<button type="button" class="btn" onclick="Admin.updateAdjustment({{ $user->id }})">
									Submit
								</button></td>
							</tr>

							<tr>
								<td> Total adjustments available: </td>
								<td id="availableAdjustment"> {{ displayMoney($adj) }} </td>

							</tr>

							@if($adj > 0)

							<tr>
								<td colspan="3">
								<table id="adjustmentTable">

									<thead>
										<tr>
											<th style="width: 234px;">Date Added</th>
											<th>Amount</th>
										</tr>
									</thead>

									<tbody>
										@foreach($adjustment as $adjust)

										@if($adjust->amount > 0)
										<tr>
											<td>{{ $adjust->created_at }}</td>
											<td>${{ $adjust->amount }}</td>
										</tr>
										@endif

										@endforeach
									</tbody>
								</table></td>
							</tr>

							@endif

							<tr>
								<td><strong>Minimum: </strong></td>
								<td>
								<input value="{{ $min }}" onkeyup="Admin.updateMinimum(this, {{ $user->id }})" size="8">
								</td>
							</tr>

							@php

							$prices = [];

							foreach($user->prices() as $price){
							$prices[] = $price;
							}
							//$prices = $user->prices();
							//$prices->toArray();
							@endphp

							@foreach($types as $type)

							@php
							Log::info( json_encode($prices) );
							//$price = $prices::find($type->id);
							//Log::info($price->amount);
							@endphp

							<tr>
								<td><strong> {{$type->title}} </strong></td>
								<td>
								<input onchange="Admin.updateUserPrice(this, {{ $user->id }});" type='text' value='{{ $user->priceArray()["$type->id"] }}' data-id="{{ $type->id }}">
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</p>
			</div>
		</div>
	</div>
</div>
@endif

<div class="row tab-content" id="user_permissions">
	<div class="col-md-12">
		<div class="panel panel-danger">
			<div class="panel-body permissions">
				<!--h4>Statistics</h4>

				< table class="table table-condensed">

				<tr>
				<th>Logs</th>
				<td>{{ $user->logs()->count() }}</td>
				</tr>

				<tr>
				<th>Transactions</th>
				<td>{{ $user->transactions()->count() }}</td>
				</tr>
				</table >

				<h4>Permissions</h4-->

				<hr>

				<p>
					<a href="{{ secure_url('admin/users/'.$user->id.'/disguise') }}" class="btn bg-navy"> Login as {{ $user->first_name }} </a>
				</p>
				<hr>
				<p>
					@if (!$user->is_approved)
					<a href="{{ secure_url('admin/users/'.$user->id.'/approve') }}" class="btn bg-navy"> Approve {{ $user->first_name }}'s Account </a>
					@else
					<a href="{{ secure_url('admin/users/'.$user->id.'/disapprove') }}" class="btn btn-danger"> Disapprove {{ $user->first_name }}'s Account </a>
					@endif
				</p>
				<hr>
				<p>
					@if (!$user->company_rep)
					<a href="{{ secure_url('admin/users/'.$user->id.'/authorizedrep') }}" class="btn bg-navy" data-confirm-text="This sets the user as the account owner."> Set as Account Owner </a>
					@else
					<a href="{{ secure_url('admin/users/'.$user->id.'/notauthorizedrep') }}" class="btn btn-danger" data-confirm-text="Do not remove this user as the account owner unless someone can be made the account owner. This is a very big deal. Make SURE there is another account woner available."> Remove as Account Owner </a>
					@endif
				</p>
				<hr>
				<p>
					<a href="{{ secure_url('admin/users/'.$user->id.'/delete') }}" class="btn btn-danger confirm-link" data-confirm-text="Deleting a user will also delete all customers, transactions, reviews, emails, text messages, and EVERYTHING else associated with that user. This is a big deal! Please only do this if you're REALLY sure about it."> Delete User </a>
				</p>
				<hr>
				<p>
					@if (!$user->hasRole('admin'))
					<a href="{{ secure_url('admin/users/'.$user->id.'/promote') }}" class="btn bg-navy confirm-link" data-confirm-text="Making this user an admin will give them access to EVERYTHING. This is a HUGE deal! Only do this if you're absolutely sure."> Grant Admin Rights </a>
					@else
					<a href="{{ secure_url('admin/users/'.$user->id.'/demote') }}" class="btn btn-danger confirm-link"> Revoke Admin Rights </a>
					@endif
				</p>
				<hr>
				<p>
					@if (!$user->hasRole('apiauth'))
					<a href="{{ secure_url('admin/users/'.$user->id.'/apiuser') }}" class="btn bg-navy confirm-link" data-confirm-text="This will allow a user to have API access."> Grant API Access </a>
					@else
					<a href="{{ secure_url('admin/users/'.$user->id.'/notapiuser') }}" class="btn btn-danger confirm-link"> Revoke API Access </a>
					@endif
				</p>
				<hr>
				<p>
					@if (!$user->hasRole('limited_admin'))
					<a href="{{ secure_url('admin/users/'.$user->id.'/limitedadmin') }}" class="btn bg-navy confirm-link" data-confirm-text="This will allow an administrator to set limited administration roles for a user."> Grant Limited Admin Access </a>
					@else
					<a href="{{ secure_url('admin/users/'.$user->id.'/notlimitedadmin') }}" class="btn btn-danger confirm-link"> Revoke Limited Admin Access </a>
					@endif
				</p>

			</div>
		</div>
	</div>

</div>

@if($user->hasRole('limited_admin'))
<div class="row tab-content" id="admin_permissions">
	@php
	$companies = DB::table("users")
	->distinct('company_name')
	->where('company_rep', true)
	->orderBy('company_name', 'ASC')->get();

	$viewable = DB::table("viewable_companies")->where('user_id', $user->id)->pluck('company_id')->toArray();
	@endphp

	<div class="col-md-12">
		<div class="panel panel-danger">
			<div class="panel-body text-center limited-admin col-md-4">
				<h4>Limited Admin Permissions</h4>

				<div class="form-group ">
					{!! Form::label('viewable_companies', 'Select Viewable Companies') !!}
					<select class="form-control limited-admin-value" name="viewable_companies[]" id="viewable_companies" multiple data-id="{{ $user->id }}">
						<option value=""></option>
						@foreach($companies as $company)

						@if( isset($company->company_name))

						@if( $viewable && in_array($company->company_id, $viewable) )
						<option value="{{$company->company_id}}" selected>{{ $company->company_name }}</option>
						@else
						<option value="{{$company->company_id}}">{{ $company->company_name }}</option>
						@endif

						@endif

						@endforeach
					</select>

					</form>
					<br><br>
					<a href="#" disabled class="report_link"> <span onclick="Admin.limitedAdminCompanyReportLast({{ $user->id }})"> <i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export Limited Admin Company Report</span> </span> </a>
				</div>

				<div>
					<button class="btn btn-primary btn-lg btn-block" onclick="Admin.updateLimitedAdmin()">
						Update
					</button>
				</div>

			</div>
		</div>
	</div>
</div>
@endif

<script>
	document.addEventListener("DOMContentLoaded", function() {

		var data = {
			company_id : "{{ $user->company_id }}",
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/company/report",
		    type = "GET",
		    success = function(data) {

			generateReportHtml(data);
			$(".loading-spinner").hide(400);
			$(".stats").show(400);
		},
		    failure = function(a, b, c) {
			$(".loading-spinner").hide(400);
			$(".stats-error").show(400);
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	});

	function generateReportHtml(data) {

		$(".checks_count_for_day").html(data.checks_count_for_day);
		
		var keys = Object.keys(data.types_for_day);
		var amount = 0;
		var types = "";

		for (var i = 0; i < keys.length; i += 1) {
			var t = data.types_for_day[keys[i]];
			amount += t.amount;

			types += "<span data-type='" + t.type_id + "' data-after='" + dates.today + "' data-company='" + company_id + "' class='report-link' onclick='Admin.reportForDay(this);'>" + keys[i] + "</span><br>";
		}

		$(".amount_for_day").html("$ " + amount.toFixed(2));
		$(".types_for_day").html(types);

		$(".checks_count_for_month").html(data.checks_count_for_month);
		var mkeys = Object.keys(data.types_for_month);
		var mamount = 0;
		var mtypes = "";

		for (var i = 0; i < mkeys.length; i += 1) {
			var t = data.types_for_month[mkeys[i]];
			mamount += t.amount;

			mtypes += "<span data-type='" + t.type_id + "' data-after='" + dates.month_td_start + "' data-company='" + company_id + "' class='report-link'  onclick='Admin.reportForMonth(this);'>" + mkeys[i] + "</span><br>";

		}

		$(".amount_for_month").html("$ " + mamount.toFixed(2));
		$(".types_for_month").html(mtypes);

		$(".checks_count_for_previous_month").html(data.checks_count_for_previous_month);
		var pkeys = Object.keys(data.types_for_previous_month);
		var pamount = 0;
		var ptypes = "";

		for (var i = 0; i < pkeys.length; i += 1) {

			var t = data.types_for_previous_month[pkeys[i]];
			pamount += t.amount;

			ptypes += "<span data-type='" + t.type_id + "' data-after='" + dates.month_lst_start + "' data-before='" + dates.month_lst_end + "' data-company='" + company_id + "' class='report-link' onclick='Admin.reportForLastMonth(this);'>" + pkeys[i] + "</span><br>";

		}

		$(".amount_for_previous_month").html("$ " + pamount.toFixed(2));
		$(".types_for_previous_month").html(ptypes);

		$(".checks_count_for_ytd").html(data.checks_count_for_ytd);
		var ykeys = Object.keys(data.types_for_ytd);
		var yamount = 0;
		var ytypes = "";

		for (var i = 0; i < ykeys.length; i += 1) {
			var t = data.types_for_ytd[ykeys[i]];
			yamount += t.amount;

			ytypes += "<span data-type='" + t.type_id + "' data-after='" + dates.ytd_start + "' data-company='" + company_id + "' class='report-link' onclick='Admin.reportForYtd(this);'>" + ykeys[i] + "</span><br>";
		}

		$(".amount_for_ytd").html("$ " + yamount.toFixed(2));
		$(".types_for_ytd").html(ytypes);

	}

	var dates = {
		today : "{{ Carbon\Carbon::today()->subDay()->toDateString() }}",
		month_td_start : "{{ Carbon\Carbon::now()->startOfMonth()->toDateString() }}",
		month_lst_start : "{{ Carbon\Carbon::now()->startOfMonth()->subMonth()->toDateString() }}",
		month_lst_end : "{{ Carbon\Carbon::now()->startOfMonth()->toDateString() }}",
		ytd_start : "{{ Carbon\Carbon::now()->subYear()->toDateString() }}",
		ytd_end : "{{ Carbon\Carbon::now()->toDateString() }}"
	}

	var company_id = "{{ $user->company_id }}";

</script>

@endsection