@extends('layouts.app', [
    'title' => 'Edit Transactions'
])

@section('content')

<input id="transactionId" value="">
  <br>
  <br>
<button class="submit btn btn-primary" onclick="getTransaction();">Search</button>
  <br>
  <br>

<div class="panel">

	<table class="table table-striped table-hover">
		
		<tr>
			<th>ID</th>
			<td>
				<span id="trans_id"></span>
			</td>
		</tr>
		<tr>
			<th>Created At</th>
			<td>
				<span id="trans_created"></span>
			</td>
		</tr>
		<tr>
			<th>Amount</th>
			<td>
				<input class="col-md-4" id="trans_amount" value="">
			</td>
		</tr>
		<tr>
			<th>Description:</th>
			<td>
				<textarea class="col-md-4" id="trans_description"></textarea>
			</td>
		</tr>
		<tr>
			<th>Notes</th>
			<td>
				<input class="col-md-4" id="trans_notes" value="">
			</td>
		</tr>
		
	</table>
    	<br>
    	<br>
	<button class="submit btn btn-primary" onclick="saveTransaction();">Save</button>
</div>

<script>

    var transactionId = null;

    function getTransaction(){
    	
    	const checkId = $("#transactionId").val();
    	
    	const data = {
				_token: $('meta[name="csrf-token"]').attr('content')
			},
			url = "/admin/transactions/" + checkId,
			type = "GET",
			success = function(data){
                //console.log(data);
                populateTransaction(data);
			},
			failure = function(a,b,c){
				console.log("Failed");
				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);
    	
    	
    }
    
    function saveTransaction(){
    	
    	const data = {
				_token: $('meta[name="csrf-token"]').attr('content'),
				amount: $("#trans_amount").val(),
				description: $("#trans_description").val(),
				notes: $("#trans_notes").val(),
				id: transactionId
			},
			url = "/admin/transactions/update",
			type = "POST",
			success = function(data){
				
                console.log(data);
                alert("Transaction has been updated.");
                
                $("#trans_id").html("");
		    	transactionId = 0;
		    	$("#trans_created").html("");
		    	$("#trans_amount").val("");
		    	$("#trans_description").val("");
		    	$("#trans_notes").val("");
  
			},
			failure = function(a,b,c){
				console.log("Failed");
				console.log(JSON.stringify(a));
				console.log(JSON.stringify(b));
				console.log(JSON.stringify(c));
			},
			request = su.requestObject(url, type, success, failure, data);
	
	    su.asynch(request);
    	
    }
    
    function populateTransaction(data){
    	
    	$("#trans_id").html(data.id);
    	transactionId = data.id;
    	$("#trans_created").html(data.created_at);
    	$("#trans_amount").val(data.amount);
    	$("#trans_description").val(data.description);
    	$("#trans_notes").val(data.notes);
    }
	
</script>

@endsection