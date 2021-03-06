$(function() {

	$('.fadein').fadeIn('fast');

	$(".form-loading").on("submit", function() {
		$('#modal-loading').modal('show');
	});
	$(".btn-loading").click(function(e) {
		$('#modal-loading').modal('show');
	});

	// Sidebar Menu
	$('.sidebar-menu').tree();

	$(".sidebar-toggle").click(function() {
		$.get("/sidebar");
	});

	$('select').selectpicker();

	$(".clickable-row").click(function() {
		window.location = $(this).data("href");
	});
	$(".clickable-cell").click(function() {
		window.location = $(this).data("href");
	});

	$(".admin-reports-index .table-toggle").click(function() {

		//$("div.col-md-4").css("position", "static");
		$(".table-toggle").next().hide(400);

		//$(this).closest("div.col-md-4").css("position", "absolute");
		$(this).next().css('display', 'inline-block');
	});

	$('[data-toggle="popover"]').popover();

	// Sweet Alert
	$('body').on('submit', '.confirm-form-submit', function(e) {
		e.preventDefault();
		var self = this;
		var text = $(this).data('confirm-text');
		swal({
			title : "Are you sure?",
			type : "warning",
			text : text,
			showCancelButton : true,
			confirmButtonColor : "#DD6B55",
			confirmButtonText : "Yes, I'm sure",
			closeOnConfirm : true
		}, function() {
			self.submit();
		});
	});

	$('body').on('click', '.confirm-link', function(e) {
		e.preventDefault();
		var self = this;
		var text = $(this).data('confirm-text');
		swal({
			title : "Are you sure?",
			text : text,
			type : "warning",
			showCancelButton : true,
			confirmButtonColor : "#DD6B55",
			confirmButtonText : "Yes, I'm sure",
			closeOnConfirm : true
		}, function() {
			location.href = self.href;
		});
	});

	//after//before//type//search=true

	//https://dev.eyeforsecurity.com/admin/checks?
    /*
	$("table.checks-by-day tr").click(function() {
		var type = $(this).data("type"),
		    before = $(this).data("before"),
		    after = $(this).data("after"),
		    company_id = $(this).data("company") ? $(this).data("company") : null;

		Admin.getCheck(before, after, type, company_id);
	});

	$("table.checks-this-month tr").click(function() {
		var type = [$(this).data("type")],
		    before = $(this).data("before"),
		    after = $(this).data("after"),
		    company_id = $(this).data("company") ? $(this).data("company") : null;

		Admin.getCheck(before, after, type, company_id);
	});

	$("table.checks-last-month tr").click(function() {
		var type = [$(this).data("type")],
		    before = $(this).data("before"),
		    after = $(this).data("after"),
		    company_id = $(this).data("company") ? $(this).data("company") : null;

		Admin.getCheck(before, after, type, company_id);
	});

	$("table.checks-ytd tr").click(function() {
		var type = [$(this).data("type")],
		    before = $(this).data("before"),
		    after = $(this).data("after"),
		    company_id = $(this).data("company") ? $(this).data("company") : null;

		Admin.getCheck(before, after, type, company_id);
	});
	*/

});

const Admin = {

	getCheck : function(before, after, type, company) {

		var url = "/admin/checks?before=" + before + "&after=" + after + "&type[]=" + type + "&company_id[]=" + company + "&search=true";
		window.location.href = url;

	},

	editCheck : function(report) {
		$(".report-edit-wrapper").show();

		//populate the panel
		$(".report-edit-panel").html("<pre>" + report + "</pre>");

	},

	saveCheck : function(report) {
		//put
	},

	cancelCheck : function() {
		$(".report-edit-wrapper").remove();
	},
	
	getCustomReport: function(reportType){
		
		$(".progress-overlay").show(400);
		console.log("getCustomReport");
		console.log("Report type is " + reportType);
		
		var data = {
				_token : $('meta[name="csrf-token"]').attr('content'),
				start : $("#start_date").val(),
				end : $("#end_date").val(),
				company: $("#company").val(),
				report_type : reportType
			},
		    success = function(data) {
		    	
		    	//console.log(data);
		    	
		    	$(".progress-overlay").hide(400);

                
				var date = new Date(),
				    name = (date.getMonth() + 1) + "-" + date.getDate() + "-" + date.getFullYear() + "_custom_export.csv";
	
				var filename = name;
				var data = data.report;
				
				var blob = new Blob([data], {
					type : 'text/csv'
				});
				if (window.navigator.msSaveOrOpenBlob) {
					window.navigator.msSaveBlob(blob, filename);
				} else {
					var elem = window.document.createElement('a');
					elem.href = window.URL.createObjectURL(blob);
					elem.download = filename;
					document.body.appendChild(elem);
					elem.click();
					document.body.removeChild(elem);
				}
	            
			},
		    failure = function(a, b, c) {
		    	$(".progress-overlay").hide(400);
		    	console.log(JSON.stringify(a));
		    	console.log(JSON.stringify(b));
		    	console.log(JSON.stringify(c));
			},
		    url = "/api/admin/reports/custom",
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);
		    
		su.asynch(request);
	},
	
	getCustomReportData: function(reportType){
		
		$(".progress-overlay").show(400);

		var data = {
				_token : $('meta[name="csrf-token"]').attr('content'),
				start : $("#start_date").val(),
				end : $("#end_date").val(),
				company: $("#company").val(),
				report_type : reportType
			},
		    success = function(data) {
		    	
		    	$(".progress-overlay").hide(400);
		    	
		    	var filename = reportType + "_report.csv";
				var data = data.report;
				
				Admin.showCsvDataInModal(data);
				
				/*
				if($reportType == 'adminDashboard'){
					//disply the pop-up modal
				}else{
					var blob = new Blob([data], {
						type : 'text/csv'
					});
					if (window.navigator.msSaveOrOpenBlob) {
						window.navigator.msSaveBlob(blob, filename);
					} else {
						var elem = window.document.createElement('a');
						elem.href = window.URL.createObjectURL(blob);
						elem.download = filename;
						document.body.appendChild(elem);
						elem.click();
						document.body.removeChild(elem);
					}	
				}
				*/
					
		    	
		    	
			},
		    failure = function(a, b, c) {
		    	$(".progress-overlay").hide(400);
		    	console.log(JSON.stringify(a));
		    	console.log(JSON.stringify(b));
		    	console.log(JSON.stringify(c));
			},
		    url = "/api/admin/reports/custom",
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);
		    
		su.asynch(request);
		
	},

	getInvoices : function() {
		
		Admin.getCustomReport();
	
	},

	getCompanies : function() {

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    success = function(data) {

			var date = new Date(),
			    name = (date.getMonth() + 1) + "-" + date.getDate() + "-" + date.getFullYear() + "_company_export.csv";

			var filename = name;
			var data = data.report;
			var blob = new Blob([data], {
				type : 'text/csv'
			});
			if (window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;
				document.body.appendChild(elem);
				elem.click();
				document.body.removeChild(elem);
			}

		},
		    failure = function(a, b, c) {
		},
		    url = '/api/users/company/export/all',
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	},
	
	getUsers: function(){
		
		var data = {
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    success = function(data) {

			var date = new Date(),
			    name = (date.getMonth() + 1) + "-" + date.getDate() + "-" + date.getFullYear() + "_users_export.csv";

			var filename = name;
			var data = data.report;
			var blob = new Blob([data], {
				type : 'text/csv'
			});
			if (window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;
				document.body.appendChild(elem);
				elem.click();
				document.body.removeChild(elem);
			}

		},
		    failure = function(a, b, c) {
		},
		    url = '/api/users/company/export/users',
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
		
	},

	exportCompany : function(self) {

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content'),
		},
		    success = function(data) {

			var name = $(self).data("company") + "_company_export.csv";

			var filename = name;
			var data = data.report;
			var blob = new Blob([data], {
				type : 'text/csv'
			});
			if (window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;
				document.body.appendChild(elem);
				elem.click();
				document.body.removeChild(elem);
			}

		},
		    failure = function(a, b, c) {
		},
		    url = '/api/users/company/export/' + $(self).data("company"),
		    type = "GET",
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	updateAdjustment : function(cId) {

		if (isNaN($("#adjustmentAmount").val())) {
			alert("Adjustment must be a number");
		}

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content'),
			amount : $("#adjustmentAmount").val(),
			cId : cId
		},
		    url = "/api/pricing/adjustment",
		    type = "POST",
		    request = su.requestObject(url, type, function(data) {
		    	
			console.log("Success");
			//repopulate the pricing table
			$("#adjustmentTable tbody").html(data.html);
			$("#availableAdjustment").html(data.amount);
			$("#adjustmentAmount").val(0);
			
		}, function(a, b, c) {
			console.log("Failure");
		}, data);

		su.asynch(request);

	},

	updateMinimum : function(self, cId) {

		if (isNaN($(self).val())) {
			alert("Adjustment must be a number");
		}

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content'),
			amount : $(self).val(),
			cId : cId
		},
		    url = "/api/pricing/minimum",
		    type = "POST",
		    request = su.requestObject(url, type, function(data) {
			console.log("Success");
		}, function(a, b, c) {
			console.log("Failure");
		}, data);

		su.asynch(request);
	},

	updateUserPrice : function(self, cId) {

		if (isNaN($(self).val())) {
			alert("Price must be a number");
		}

		//$id = $(self).data("id"),
		var data = {
			pId : $(self).data("id"),
			amount : $(self).val(),
			cId : cId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/pricing/user",
		    type = "POST",
		    request = su.requestObject(url, type, function(data) {
			console.log("Success");
		}, function(a, b, c) {
			console.log("Failure");
		}, data);

		su.asynch(request);
	},

	updateStatePrice : function(self, sId) {

		if (isNaN($(self).val())) {
			alert("Price must be a number");
		}

		//$id = $(self).data("id"),
		var data = {
			amount : $(self).val(),
			sId : sId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/pricing/state",
		    type = "POST",
		    request = su.requestObject(url, type, null, null, data);

		su.asynch(request);
	},

	updateStateExtra : function(self, sId) {
		if (isNaN($(self).val())) {
			alert("Price must be a number");
		}

		var data = {
			amount : $(self).val(),
			sId : sId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/pricing/state/extra",
		    type = "POST",
		    request = su.requestObject(url, type, null, null, data);

		su.asynch(request);

	},

	updateBaseCheckPrice : function(self, tId) {

		if (isNaN($(self).val())) {
			alert("Price must be a number");
		}

		var data = {
			amount : $(self).val(),
			tId : tId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/pricing/base",
		    type = "POST",
		    request = su.requestObject(url, type, null, null, data);

		su.asynch(request);
	},

	updateCountyExtra : function(self, cId) {

		if (isNaN($(self).val())) {
			alert("Price must be a number");
		}

		var data = {
			amount : $(self).val(),
			cId : cId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/pricing/county/extra",
		    type = "POST",
		    request = su.requestObject(url, type, null, null, data);

		su.asynch(request);

	},

    //stupidly named function
	updateCompanyId : function(self) {

		//if ($(self).val().length >= 2  || $(self).data("type") == "invoice") {
			var data = {
				id : $(self).data("id"),
				val : $(self).val(),
				type : $(self).data("type"),
				_token : $('meta[name="csrf-token"]').attr('content')
			},
			    url = "/api/users/company/id/update",
			    type = "PUT",
			    request = su.requestObject(url, type, null, null, data);

			su.asynch(request);
		//}

	},
	
	resetTou: function(self){
		
		var data = {
				id : $(self).data("id"),
				val : null,
				type : 'tou_reset',
				_token : $('meta[name="csrf-token"]').attr('content')
			},
			    url = "/api/users/company/id/update",
			    type = "PUT",
			    request = su.requestObject(url, type, null, null, data);

			su.asynch(request);
		
	},

	registerAch : function(plaid_token, account_id) {

		var data = {
			plaid_token : plaid_token,
			account_id : account_id,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/settings/ach",
		    type = "POST",
		    success = function(data) {
			console.log(data);
		},
		    failure = function(a, b, c) {
			alert("ach registration failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	},

	reportForDay : function(self) {

		var type = [$(self).data("type")],
		    after = $(self).data("after"),
		    company_id = $(self).data("company");

		Admin.getCheck(null, after, type, company_id);
	},

	reportForMonth : function(self) {

		var type = [$(self).data("type")],
		    after = $(self).data("after"),
		    company_id = $(self).data("company");

		Admin.getCheck(null, after, type, company_id);
	},

	reportForLastMonth : function(self) {

		var type = [$(self).data("type")],
		    after = $(self).data("after"),
		    company_id = $(self).data("company"),
		    before = $(self).data("before");

		Admin.getCheck(before, after, type, company_id);
	},

	reportForYtd : function(self) {

		var type = [$(self).data("type")],
		    after = $(self).data("after"),
		    company_id = $(self).data("company");

		Admin.getCheck(null, after, type, company_id);
	},

	updateLimitedAdmin : function() {

		var data = {
			viewable_companies : $("#viewable_companies").val(),
			user_id : $("#viewable_companies").data("id"),
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/settings/limitedAdmin",
		    type = "POST",
		    success = function(data) {
			console.log(data);
			alert("Limited Permissions Updated");
		},
		    failure = function(a, b, c) {
			alert("Limited admin update failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	limitedAdminCompanyReport : function(id) {

		var data = {
			user_id : id,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/reports/limitedAdmin/companyReport",
		    type = "GET",
		    success = function(data) {

			var name = "company_export.csv";

			var filename = name;
			//var data = data;
			var blob = new Blob([data], {
				type : 'text/csv'
			});
			if (window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;
				document.body.appendChild(elem);
				elem.click();
				document.body.removeChild(elem);
			}
		},
		    failure = function(a, b, c) {
			alert("Limited admin companyReport failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	limitedAdminCompanyReportLast : function(id) {

		var data = {
			user_id : id,
			previous : true,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/reports/limitedAdmin/companyReport",
		    type = "GET",
		    success = function(data) {

			var name = "company_export.csv";

			var filename = name;
			//var data = data;
			var blob = new Blob([data], {
				type : 'text/csv'
			});
			if (window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			} else {
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;
				document.body.appendChild(elem);
				elem.click();
				document.body.removeChild(elem);
			}
		},
		    failure = function(a, b, c) {
			alert("Limited admin companyReport failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	toggleTabContent : function(self, content) {

		$(".tab-bar button").removeClass("btn-primary").addClass("btn-secondary");
		$(".row.tab-content").removeClass("active");

		$(self).removeClass("btn-secondary");
		$(self).addClass("btn-primary");

		$("#" + content).addClass("active");
	},

	removePaymentInfo : function(uId) {

		var data = {
			user_id : uId,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/settings/removePaymentInfo",
		    type = "POST",
		    success = function(data) {
			location.reload();
		},
		    failure = function(a, b, c) {

			console.log(JSON.stringify(a));
			console.log(JSON.stringify(b));
			console.log(JSON.stringify(c));

			alert("Limited admin update failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	editReport : function(reportId) {

		var data = {
				reportId : reportId,
				_token : $('meta[name="csrf-token"]').attr('content')
			},
		    url = "/admin/reports/show",
		    type = "GET",
		    success = function(data) {
		    	
		    	if(data.report.xml){
		    		CssiEditableReport = JSON.parse(data.report.xml);
					CssiEditableReportCopy = JSON.parse(data.report.xml);
		    	}else{
		    		CssiEditableReport = data.report;
					CssiEditableReportCopy = data.report;
		    	}
	
				$("button.edit-button-row, span.edit-button-row").toggle();
	
			},
		    failure = function(a, b, c) {
	
				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
	
				alert("Edit report failure");
			},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	saveEditReport : function(reportId, typeId) {
		
      var data = {
				reportId : reportId,
				report: JSON.stringify(CssiEditableReport),
				typeId: typeId,
				_token : $('meta[name="csrf-token"]').attr('content')
			},
		    url = "/admin/reports/update",
		    type = "POST",
		    success = function(data) {
	
				$("button.edit-button-row, span.edit-button-row").toggle();
	
			},
		    failure = function(a, b, c) {
	
				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
	
				alert("Edit report failure");
			},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
	},

	cancelEditReport : function() {

		CssiEditableReport = null;
		$("button.edit-button-row, span.edit-button-row").toggle();

	},

	toggleDataDiverRecord : function(recordId, self) {
		
		var id = parseInt(recordId);
		
		if( !$.isArray(CssiEditableReport.InstantCriminalResponse.Offender) ){
			var record = CssiEditableReport.InstantCriminalResponse.Offender;
		}else if( $.isArray(CssiEditableReport.InstantCriminalResponse.Offender) ){
			var record = CssiEditableReport.InstantCriminalResponse.Offender[recordId];
		}

		if( $(self).is(":checked") ){
			
			//see if there is a meta tag
			if(!record.meta){

				record["meta"] = {
					'date': $("#editDate").val(),
					'hide' : true,
					'admin' : $("#editAdmin").val()
				};
				
			}else{
				
				record.meta.date = $("#editDate").val();
				record.meta.hide = true;
				record.meta.admin = $("#editAdmin").val();
			}
			
			$(".meta-admin-name_" + id ).html(record.meta.admin);
			$(".meta-admin-date_" + id ).html(record.meta.date);
			$(".admin-meta-info-wrapper_" + id).show(400);

			
		}else{
			
			record.meta.date = "";
			record.meta.hide = false;
			record.meta.admin = "";
			
			$(".admin-meta-info-wrapper_" + id).hide(400);
			$(".meta-admin-name_" + id ).html("");
			$(".meta-admin-date_" + id ).html("");
   	
		}

	},

	toggleDataDiverOffense : function(recordId, offenseId, self) {
		
		let offense = [];

		if( !$.isArray(CssiEditableReport.InstantCriminalResponse.Offender) ){
		    
		    if(!$.isArray(CssiEditableReport.InstantCriminalResponse.Offender.Records) ){
		    	offense = [CssiEditableReport.InstantCriminalResponse.Offender.Records.Record];
		    }else{
		    	offense = CssiEditableReport.InstantCriminalResponse.Offender.Records.Record;
		    }
	
		}else if( $.isArray(CssiEditableReport.InstantCriminalResponse.Offender) ){

            if( $.isArray(CssiEditableReport.InstantCriminalResponse.Offender[recordId].Records.Record)){
            	offense = CssiEditableReport.InstantCriminalResponse.Offender[recordId].Records.Record;
            }else{
            	offense = [CssiEditableReport.InstantCriminalResponse.Offender[recordId].Records.Record];
            }

		}else{
			offense = null;
		}
		
		if( $(self).is(":checked") ){
			
			if (typeof offense[offenseId].meta == 'undefined') {
				
				offense[offenseId]["meta"] = {
					'date': $("#editDate").val(),
					'hide' : true,
					'admin' : $("#editAdmin").val()
				};

			}else{
				offense[offenseId].meta.date = $("#editDate").val();
				offense[offenseId].meta.hide = true;
				offense[offenseId].meta.admin = $("#editAdmin").val();
			}
			
			$(".offense-meta-admin-name_" + recordId + "_" + offenseId ).html(offense[offenseId].meta.admin);
			$(".offense-meta-admin-date_" + recordId + "_" + offenseId ).html(offense[offenseId].meta.date);
			$(".admin-meta-info-wrapper_" + recordId + "_" + offenseId ).show(400);
			
		}else{

			offense[offenseId].meta.date = "";
			offense[offenseId].meta.hide = false;
			offense[offenseId].meta.admin = "";
			
			$(".admin-meta-info-wrapper_" + recordId + "_" + offenseId ).hide(400);	
		    $(".offense-meta-admin-date_" + recordId + "_" + offenseId ).html("");
		    $(".offense-meta-admin-name_" + recordId + "_" + offenseId ).html("");
		}
		
	},
	
	
	reconcileInvoice : function(id){
		
		var data = {
			id : id,
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    url = "/api/admin/invoices/reconcile",
		    type = "POST",
		    success = function(data) {
		    	
		    //I want to return the data so I can update the table
		    //console.log(data);
		    const recordId = data.id;
		    
		    $("." + recordId + "_inv .reconciled_by").html(data.reconciled_by);
		    $("." + recordId + "_inv .reconciled_date").html(data.reconciled_date);

		},
		    failure = function(a, b, c) {

			console.log(JSON.stringify(a));
			console.log(JSON.stringify(b));
			console.log(JSON.stringify(c));

			alert("Reconcile invoice failure");
		},
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);
		
	},
	
	streamInvoice: function(id){
	
		let data = {
				_token : $('meta[name="csrf-token"]').attr('content'),
				invoiceId : id
			},
			basePath = $("#basePath").val();
		
		$.ajax({
                type : "GET",
                url : basePath + "/invoices/stream",
                success : function(data) {
		    		
					var blob = new Blob([data], {
						type : 'application/pdf'
					});
					
		
					if (window.navigator.msSaveOrOpenBlob) {
						window.navigator.msSaveBlob(blob, "Invoice");
					} else {
						var elem = window.document.createElement('a');
						elem.href = window.URL.createObjectURL(blob);
						elem.download = "Invoice";
						document.body.appendChild(elem);
						elem.click();
						document.body.removeChild(elem);
					}

				},
                error : function(a, b, c) {
			    	$(".progress-overlay").hide(400);
			    	console.log(JSON.stringify(a));
			    	console.log(JSON.stringify(b));
			    	console.log(JSON.stringify(c));
				},
                data : data,
            }).always(function() {
            	//$(".modalWindowCover").fadeOut(400);
            });
	
		},
		
		toggleCheckEnable: function(id){
			
			var data = {
				typeId : id,
				_token : $('meta[name="csrf-token"]').attr('content')
			},
			    url = "/admin/types/toggle",
			    type = "GET",
			    success = function(data) {
					
			},
			    failure = function(a, b, c) {
	
				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
	
				alert("Toggle check failure");
			},
			    request = su.requestObject(url, type, success, failure, data);
	
			su.asynch(request);
			
		},
		
		showCsvDataInModal: function(csv){
						
			let lines = csv.split("\n"),
			    html = "<table class='csv_data_table'>",
			    cnt = 0;
			    
			lines.forEach(function(line){
				
				if(line){

					var tuples = line.split(",");

					if(cnt == 0){
					
						html += "<thead><tr>" +
								"  <th>" + tuples[0] + "</th>" +
								"  <th>" + tuples[1] + "</th>" +
								"  <th>" + tuples[2] + "</th>" +
								"</tr></thead>";
						
						cnt += 1;
						
					}else{
						html += "<tr data-type='" + tuples[6] + "' data-after='" + tuples[4] + "' data-before='" + tuples[5] + "' data-company='" + tuples[3] +"' onclick='Admin.clickShowChecks(this);'>" +
								"  <td>" + tuples[0] + "</td>" +
								"  <td>" + tuples[1] + "</td>" +
								"  <td>" + tuples[2] + "</td>" +
								"</tr>";
					}
					
				}
				
			});
			
			html += "</table>";
			
			BaseModal.display(html);
			
		},
		
		clickShowChecks: function(self){
			
			var before = $(self).data("before");
			
			if( $(self).data("type") == "adminDashboard" ){
				before ='';
			}
		
			//$(".check-types tr").click(function(){
				var type = $(self).data("type"),
				    before = before,
				    after = $(self).data("after"),
				    company_id = $(self).data("company") ? $(self).data("company") : null;
				    

				Admin.getCheck(before, after, type, company_id);
			//});
		},
		
		getProfile: function(checkId){
			
			var data = {
				checkId : checkId,
				_token : $('meta[name="csrf-token"]').attr('content')
			},
		    url = "/checks/profile",
		    type = "GET",
		    success = function(data) {

				Checks.populateInput(data);
			},
		    failure = function(a, b, c) {

				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
	
				console.log("FAILED TO GET PROFILE");
			},
		    request = su.requestObject(url, type, success, failure, data);
	
			su.asynch(request);
			
		},
		
		getRawTotals: function(){
			
			var data = {
				_token : $('meta[name="csrf-token"]').attr('content')
			},
		    url = "/admin/reports/rawtotals",
		    type = "GET",
		    success = function(data) {
		    	
		    	var blob = new Blob([data], {
					type : 'text/csv'
				});
				
	
				if (window.navigator.msSaveOrOpenBlob) {
					window.navigator.msSaveBlob(blob, "Raw_Totals");
				} else {
					var elem = window.document.createElement('a');
					elem.href = window.URL.createObjectURL(blob);
					elem.download = "Raw_Totals";
					document.body.appendChild(elem);
					elem.click();
					document.body.removeChild(elem);
				}
		    	
		    	
		    	
				//Checks.populateInput(data);
			},
		    failure = function(a, b, c) {

				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
	
				console.log("FAILED TO GET PROFILE");
			},
		    request = su.requestObject(url, type, success, failure, data);
	
			su.asynch(request);
			
			
		}
	
};

CssiEditableReport = null;
CssiEditableReportCopy = null;

const Settings = {

	toggleSandboxOn : function() {

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    success = function(data) {
			location.reload();
		},
		    failure = function(a, b, c) {
		},
		    url = "/api/sandbox/enable",
		    type = "POST",
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	},

	toggleSandboxOff : function() {

		var data = {
			_token : $('meta[name="csrf-token"]').attr('content')
		},
		    success = function(data) {
			location.reload();
		},
		    failure = function(a, b, c) {
		},
		    url = "/api/sandbox/disable",
		    type = "POST",
		    request = su.requestObject(url, type, success, failure, data);

		su.asynch(request);

	},
};

const BaseModal = {
	
	display: function(html){
		console.log("Display modal");
		$(".baseModalContent").html(html);
		$(".baseModal").addClass("base_modal_display");
	},
	
	remove: function(){
		$(".baseModal").removeClass("base_modal_display");
		$(".baseModalContent").html("");
	}
	
};

const Checks = {
	
	populateInput: function(check){
		
		$("#first_name").val(check.first_name);
		$("#middle_name").val(check.middle_name);
		$("#last_name").val(check.last_name);
		
		try{
			var profile = JSON.parse(check.profile.profile);
			
			//************* DATE PICKER ELEMENTS
			if(document.getElementById("birthday")) {
				document.getElementById("birthday").defaultValue=profile.birthday;
			}
			
			if(document.getElementById("employment_current_hire_date")){
				document.getElementById("employment_current_hire_date").defaultValue=profile.current_hire_date;
			}
			
			if(document.getElementById("employment_past_hire_date")){
				document.getElementById("employment_past_hire_date").defaultValue=profile.past_hire_date;
			}
			
			if(document.getElementById("employment_past_end_date")){
				document.getElementById("employment_past_end_date").defaultValue=profile.past_end_date;
			} 
		    
		    //************* SELECTION ELEMENTS
		    $("#range").val(profile.range);
		    $("#license_state_id").val(profile.license_state_id).selectpicker('refresh');  //This is an MVR
		    $("#state_tri_eye_state_ids\\[\\]").val(profile.state_tri_eye_state_ids).selectpicker('refresh');
		    
		    $("#county_tri_eye_state").val(profile.county_tri_eye_state).selectpicker('refresh');
		    $("#county_tri_eye_county_ids").val(profile.county_tri_eye_county_ids).selectpicker("refresh");
		    $("#federal_state_tri_eye_state_ids\\[\\]").val(profile.federal_state_tri_eye_state_ids).selectpicker('refresh');
		    
		    $("#federal_district_tri_eye_district_ids").val(profile.federal_district_tri_eye_district_ids).selectpicker("refresh");
		    $("#employment_current_employer_state").val(profile.current_employer_state).selectpicker('refresh');
		    $("#education_college_state").val(profile.college_state).selectpicker("refresh");
		    $("#education_college_is_graduated").val(profile.college_is_graduated).selectpicker("refresh");
		    
		    
		    //*************  INPUT ELEMENTS
		    $("#ssn").attr('value', profile.ssn);
		    $("#license_number").val(profile.license_number);   
		    
		    $("#employment_current_employer_name").val(profile.current_employer_name);
		    $("#employment_current_employer_phone").val(profile.current_employer_phone);
		    $("#employment_current_employer_address").val(profile.current_employer_address);
		    $("#employment_current_employer_city").val(profile.current_employer_city);
		    $("#employment_current_employer_zip").val(profile.current_employer_zip);
		    $("#employment_current_job_title").val(profile.current_job_title);
		    
		    
		    $("#employment_past_employer_name").val(profile.past_employer_name);
		    $("#employment_past_employer_phone").val(profile.past_employer_phone);
		    $("#employment_past_employer_address").val(profile.past_employer_address);
		    $("#employment_past_employer_city").val(profile.past_employer_city);
		    $("#employment_past_employer_zip").val(profile.past_employer_zip);
		    $("#employment_past_job_title").val(profile.past_job_title);
		    
		    $("#education_college_name").val(profile.college_name);
		    $("#education_college_phone").val(profile.college_phone);
		    $("#education_college_address").val(profile.college_address);
		    $("#education_college_city").val(profile.college_city);
		    $("#education_college_zip").val(profile.college_zip);
		    $("#education_college_years_attended").val(profile.college_years_attended);
		    $("#education_college_graduated_year").val(profile.college_graduated_year);
		    $("#education_college_degree_type").val(profile.college_degree_type);
		    
		    $("#education_high_school_name").val(profile.high_school_name);
		    $("#education_high_school_phone").val(profile.high_school_phone);
		    $("#education_high_school_address").val(profile.high_school_address);
		    $("#education_high_school_city").val(profile.high_school_city);
		    $("#education_high_school_zip").val(profile.high_school_zip);
		    $("#education_high_school_years_attended").val(profile.high_school_years_attended);
		    $("#education_high_school_graduated_year").val(profile.high_school_graduated_year);
		    $("#education_high_school_degree_type").val(profile.high_school_degree_type);
	
		}catch(error){
			console.log("Error:: " + error);
		}
		
		/*
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		$("#").val();
		*/
		
	}
};
