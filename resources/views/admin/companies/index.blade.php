@extends('layouts.app', [
'title' => 'Companies',
'active_menu_parent' => 'admin',
'active_menu_item' => 'companies'
])

<?php

  $companiesMap = [];
  
  sort($companies);
  
  foreach($companies as $company){
      
	 $companiesMap[$company["company_id"]] = $company;
	  
     //echo json_encode($company) . "<br><br>";
  }

?>

@section('content')

<style>
	.company_charts, .company_info, .company_prices {
		/*display: none;*/
	}
</style>



<div class="companies_header row">

	<div class="col-md-12">

		<select class="company_select">
			<option value="null"></option>

			@foreach($companiesMap as $company)
			<option value="{{ $company["company_id"] }}">{{ $company["company_name"] }}</option>
			@endforeach

		</select>

	</div>

</div>

<h2 class="text-center company_name_h2"></h2>

<div class="company_charts row">

	<div class="panel-body check-data panel-primary">

		<div class="stats">
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

								<div class="col-sm-12">
									<strong> Types </strong>
								</div>
								<div class="types_for_day"><table></table></div>
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

								<div class="col-sm-12">
									<strong> Types </strong>
								</div>
								<div class="types_for_month"><table></table></div>
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

								<div class="col-sm-12">
									<strong> Types </strong>
								</div>
								<div class="types_for_previous_month"><table></table></div>
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

								<div class="col-sm-12">
									<strong> Types </strong>
								</div>
								<div class="types_for_ytd"><table></table></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="row">

	<div class="col-md-4 company_info border-primary">
		<table class="">
			<tr>
				<td><strong class="form-control">Company:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="company_name" data-type="company_name" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Company ID:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="company_id" data-type="company_id" onkeyup="COMPANY.updateCompanyId(this)" value="">
				<input type="hidden" id="original_company_id" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Address (Line 1):</strong></td>
				<td>
				<input class="admin-company-id form-control" id="address" data-type="address" onkeyup="Admin.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Address (Line 2):</strong></td>
				<td>
				<input class="admin-company-id form-control" id="secondary_address" data-type="secondary_address" onkeyup="Admin.updateCompanyId(this)" value="">
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">City:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="city" data-type="city" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">State:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="state" data-type="state" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">Zip:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="zip" data-type="zip" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">Email:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="email" data-type="email" value="" disabled>
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Invoice Recipients:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="invoice" data-type="invoice" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Phone:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="phone" data-type="phone" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Extension:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="extension" data-type="extension" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Cell Phone:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="cell_phone" data-type="cell_phone" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">Company Rep:</strong></td>
				<td class="company_rep" id="company_rep" onkeyup="COMPANY.updateCompanyId(this)">
				
				</td>
			</tr>

			<tr>
				<td><strong class="form-control">Website:</strong></td>
				<td>
				<input class="admin-company-id form-control" id="website" data-type="website" onkeyup="COMPANY.updateCompanyId(this)" value="">
				</td>
			</tr>
			<tr>
				<td><strong class="form-control">Credit Card:</strong></td>
				<td>
				<input class="admin-company_id form-control" id="ccNum" value="" disabled>
				</td>
			</tr>
		</table>
	</div>

	<div class="col-md-4 company_prices">
		<table class="">
			<tbody></tbody>
		</table>
	</div>

</div> {{-- end of row --}}

<script>
	@php

	$temp = json_encode($companiesMap);
	$temp = html_entity_decode($temp,ENT_QUOTES | ENT_HTML5);

	$checkTypes = App\Models\Type::all();
	$temp1 = json_encode($checkTypes);
	$temp1 = html_entity_decode($temp1,ENT_QUOTES | ENT_HTML5);

	@endphp

	var companiesObject = 
 <?php  echo $temp; ?>
	;
	var checkTypes =  
 <?php echo $temp1; ?>
	;

	//alert(typeof )

	function showSelectedCompanyInfo(id) {

		var company = companiesObject[id],
		    employees = company['employees'],
		    selectHtml = "",
		    ccNum = "";
		    
    
		selectHtml += "<select class='rep_select form-control'>";
		   
		for(var i=0; i < employees.length; i += 1){
			
			//console.log(employees[i]);
			//console.log("\n\n");
			selectHtml += "<option value='" + employees[i].id + "'>" + employees[i].first_name + " " + employees[i].last_name + "</option>";
		}
		
		selectHtml += "</select>";
		
		//console.log(selectHtml);

		$("#company_name").val(company['company_name']);
		$(".company_name_h2").html(company['company_name']);
		

		$("#original_company_id").val(company['company_id']);
		$("#company_id").val(company['company_id']);
		$("#address").val(company['address']);
		$("#secondary_address").val(company['secondary_address']);
		$("#city").val(company['city']);
		$("#state").val(company['state']);
		$("#zip").val(company['zip']);
		$("#email").val(company['email']);
		$("#invoice").val(company['invoice_recipients']);
		$("#phone").val(company['phone']);
		$("#extension").val(company['extension']);
		$("#cell_phone").val(company['cell_phone']);
		
		$(".company_rep").html(selectHtml);
		
		$("#website").val(company['website']);
		
		if(company['card_last_four']){
			ccNum = '****-****-****-' + company['card_last_four'];
		}

		$("#ccNum").val(ccNum);

		$(".company_info").show();

		var prices = company["prices"];

		showSelectedCompanyPrices(prices);
		getCheckTotals(company['company_id']);

	}

	function showSelectedCompanyPrices(prices) {

		var html = "";

		for (var key in prices) {

			var priceObject = prices[key];
			html += createCompanyPriceSnippet(priceObject);
		}

		$(".company_prices table tbody").html(html);
		$(".company_prices").show();

	}

	function createCompanyPriceSnippet(priceObject) {

		var type = checkTypes[(priceObject.type_id) - 1];

		return "<tr>" + "  <td><strong class='form-control'>" + type.title + "</strong></td>" + "  <td><input class='form-control' onchange='COMPANY.updateCompanyPrices(this);' type='text' data-id='" + type.id + "' value='" + priceObject.amount + "'></td>" + "</tr>";

	}

	function generateTotalsHtml(data) {

		var checksForDay = data.check_for_days,
		    checksForMonth = data.checks_for_month,
		    checksPriorMonth = data.checks_prior_month,
		    checksYtd = data.checks_ytd,
		    html = "";

		$(".checks_count_for_day").html(checksForDay.totals.count);
		$(".amount_for_day").html(checksForDay.totals.amount.toFixed(2));
		
		$(".checks_count_for_month").html(checksForMonth.totals.count);
		$(".amount_for_month").html(checksForMonth.totals.amount.toFixed(2));
		
		$(".checks_count_for_previous_month").html(checksPriorMonth.totals.count);
		$(".amount_for_previous_month").html(checksPriorMonth.totals.amount.toFixed(2));
		
		$(".checks_count_for_ytd").html(checksYtd.totals.count);
		$(".amount_for_ytd").html(checksYtd.totals.amount.toFixed(2));

		var keys = Object.keys(checksForDay.counts);

		for (var i = 0; i < keys.length; i += 1) {

			var countObject = checksForDay.counts[keys[i]];

			if (countObject.count > 0) {

				countObject["type_id"] = (i + 1);
				html += createCompanyCheckTotalSnippetForDay(countObject, dates.today, data.company_id);

			}

		}

		$(".types_for_day table").html(html);
		html = "";

		/**************************************/
		
		var keys = Object.keys(checksForMonth.counts);
		
		for (var i = 0; i < keys.length; i += 1) {

			var countObject = checksForMonth.counts[keys[i]];

			if (countObject.count > 0) {

				countObject["type_id"] = (i + 1);
				html += createCompanyCheckTotalSnippetForDay(countObject, dates.month_td_start, data.company_id);

			}

		}
		
		$(".types_for_month table").html(html);
		html = "";
		
		/************************************/

		var keys = Object.keys(checksPriorMonth.counts);
		
		for (var i = 0; i < keys.length; i += 1) {

			var countObject = checksPriorMonth.counts[keys[i]];

			if (countObject.count > 0) {

				countObject["type_id"] = (i + 1);
				html += createCompanyCheckTotalSnippetForRange(countObject, dates.month_lst_start, dates.month_lst_end, data.company_id);

			}

		}
		
		$(".types_for_previous_month table").html(html);
		html = "";
		
		/**************************************/
		
		var keys = Object.keys(checksYtd.counts);
		
		for (var i = 0; i < keys.length; i += 1) {

			var countObject = checksYtd.counts[keys[i]];

			if (countObject.count > 0) {

				countObject["type_id"] = (i + 1);
				html += createCompanyCheckTotalSnippetForDay(countObject, dates.ytd_start, data.company_id);

			}

		}
		
		$(".types_for_ytd table").html(html);
		html = "";
		
		/**************************************/
		$(".company_charts").show(400);

	}

	function createCompanyCheckTotalSnippetForRange(countObject, start, end, companyId) {

        var html = "<tr data-type='" + countObject.type_id + "' data-after='" + start + "' data-before='" + end + "' data-company='" + companyId + "'  class='report-link' onclick='Admin.reportForDay(this);'><td>" + countObject.title + "</td><td>" + countObject.count + "</td><td>$" + countObject.amount.toFixed(2) + "</td></tr>";

		return html;

	}

	//{ count: 1, title: "National Tri-Eye Check", amount: 15.95, color: "primary" }
	function createCompanyCheckTotalSnippetForDay(countObject, start, companyId) {

		var html = "<tr data-type='" + countObject.type_id + "' data-after='" + start + "' data-company='" + companyId + "'  class='report-link' onclick='Admin.reportForDay(this);'><td>" + countObject.title + "</td><td>" + countObject.count + "</td><td>$" + countObject.amount.toFixed(2) + "</td></tr>";

		return html;

	}

	function getCheckTotals(companyId) {

		var data = {
			company_id : $("#original_company_id").val(),
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/admin/company/_totals",
		    type = "GET",
		    success = function(data) {

			generateTotalsHtml(data);
			
			console.log("TOTALS:");
			console.log(data);

		},
		    failure = function(a, b, c) {
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	}

	var dates = {

		today : "{{ Carbon\Carbon::today()->startOfDay()->setTimezone('UTC')->toDateString() }}",

		month_td_start : "{{ Carbon\Carbon::today()->startOfDay()->startOfMonth()->setTimezone('UTC')->toDateString() }}",

		month_lst_start : "{{ Carbon\Carbon::today()->startOfDay()->startOfMonth()->subMonth()->setTimezone('UTC')->toDateString() }}",

		month_lst_end : "{{ Carbon\Carbon::today()->endOfDay()->subMonth()->endOfMonth()->setTimezone('UTC')->toDateString() }}",

		ytd_start : "{{ Carbon\Carbon::today()->startOfYear()->setTimezone('UTC')->toDateString() }}",

		ytd_end : "{{ Carbon\Carbon::today()->endOfDay()->setTimezone('UTC')->toDateString() }}"
	}

	document.addEventListener("DOMContentLoaded", function() {

		$(".company_select").change(function() {

            $(".company_charts").hide(400);
            //$(".company_prices").hide(400);
            //$(".company_info").hide(400);
            
			showSelectedCompanyInfo($(this).val());

		});
	});

</script>

@endsection