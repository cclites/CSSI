@extends('layouts.app', [
	'title' => 'User Management',
	'active_menu_item' => 'admin_users'
])

@section('content')

<div class="form-group">
	{!! Form::text('q', null, ['id' => 'q', 'class' => 'form-control', 'placeholder' => 'Search Users', 'autocomplete' => 'off']) !!}
</div>
<p>
<div class="list-group" id="results"></div>

@endsection

@section('js')
<script>
$(function() {
	$( "#q" ).keyup(function() {
		delay(function(){
			fetchUsers();
		}, 200 );
	});

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	function fetchUsers() {

		$.get( '{{ secure_url('api/admin/users/') }}?q=' + $( "#q" ).val(), function( results ) {

			results.data.sort(function(a, b) {
				
				if (a.company_name < b.company_name) return -1;
				if (a.company_name > b.company_name) return 1;
				
				return 0;

			});
			
			//sort the results.data
			
			html = '';
			
			
			for(var index in results.data){

			  var user = results.data[index];
				
			  var owner = "";
			  
			  if(user.company_rep){
			  	owner = "company_owner";
			  }else{
			  	owner = "company_employee";
			  }


			  html = html + 
			  '<div class="container-fluid">' +
			  '  <button data-company="' + user.companyId + '" onclick="Admin.exportCompany(this);" class="btn btn-info btn-lg company-export col-md-1"><i class="fa fa-download" aria-hidden="true"></i></button>' +
			  '  <a href="{{ secure_url('admin/users/') }}/'+user.id+'" class="list-group-item col-md-9">';
			  
			    if (user.company_name) {
	    		  	html += '    <h3 class="index-company-name">'+user.company_name+'</h3>';
	    		  }
			  
			  
    			html += '    <h4 class="list-group-item-heading ' + owner + '">'+user.full_name+'</h4>' +
    			'    <p class="list-group-item-text">Company ID: ' + user.companyId + '</p>';
    			
    		  
    		  
    		  html = html + '    <p class="list-group-item-text">'+user.email+'</p>' +
    			'    <p class="list-group-item-text">'+user.display_phone+'</p>' +
  			  '  </a>' +
  			  '</div>';
 
			//});
			}
			
			$('#results').html(html);
		});
	}

	$( "#q" ).focus();
	
	//console.log("FETCHING USERS");
	fetchUsers();
});
</script>
@endsection