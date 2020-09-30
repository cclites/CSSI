var COMPANY = {
	
	companies: [],
	
	updateCompanyId: function(self){
		
		var data = {
				id : $("#original_company_id").val(),
				val : $(self).val(),
				type : $(self).data("type"),
				_token : $('meta[name="csrf-token"]').attr('content')
			},
			    url = "/admin/company/_update",
			    type = "PUT",
			    request = su.requestObject(url, type, null, null, data);
        
		su.asynch(request);
		
	},
	
	updateCompanyPrices: function(self){
		
		var data = {
				id : $("#original_company_id").val(),
				val : $(self).val(),
				type : $(self).data("id"),
				_token : $('meta[name="csrf-token"]').attr('content')
			},
			    url = "/admin/company/prices/_update",
			    type = "PUT",
			    request = su.requestObject(url, type, null, null, data);

        su.asynch(request);
	},
	
	companySelect: function(self){
		
		var companyId = $(self).val();
		
	},
	
};
