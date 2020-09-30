@extends('layouts.app', [
    'title' => 'Configuration',
    'active_menu_parent' => 'admin',
    'active_menu_item' => 'admin_configs'
])

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
            	<h3>Configurations</h3>
            	<div class="alert alert-danger">
            	    <i class="fa fa-fire-extinguisher" aria-hidden="true"></i>
				    Warning!! Do not mess with these unless you know what you are doing.
				</div>
				
				{!! Form::open(array('url' => secure_url('admin/settings/configs'), 'method' => 'post', 'class'=>'configForm')) !!}
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_USER_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_USER_SANDBOX')}}" name="SAMBA_USER_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_ACCOUNT_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_ACCOUNT_SANDBOX')}}" name="SAMBA_ACCOUNT_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_PASS_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_PASS_SANDBOX')}}" name="SAMBA_PASS_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_WSDL_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_WSDL_SANDBOX') }}" name="SAMBA_WSDL_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_USER') }}" name="SAMBA_USER" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_ACCOUNT</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_ACCOUNT') }}" name="SAMBA_ACCOUNT" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_PASS') }}" name="SAMBA_PASS" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_WSDL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_WSDL') }}" name="SAMBA_WSDL" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SAMBA_SOAP_ACTION</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SAMBA_SOAP_ACTION') }}" name="SAMBA_SOAP_ACTION" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SECURITEC_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SECURITEC_USER') }}" name="SECURITEC_USER" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SECURITEC_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('SECURITEC_PASS') }}" name="SECURITEC_PASS" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SECURITEC_USER_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value=" {{ env('SECURITEC_USER_SANDBOX') }}" name="SECURITEC_USER_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">SECURITEC_PASS_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value=" {{ env('SECURITEC_PASS_SANDBOX') }} " name="SECURITEC_PASS_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DATADIVERS_USERNAME_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DATADIVERS_USERNAME_SANDBOX')  }}" name="DATADIVERS_USERNAME_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DATADIVERS_PASSWORD_SANDBOX</label>
					<div class="col-md-8">
				      <input class="form-control" value=" {{ env('DATADIVERS_PASSWORD_SANDBOX') }} " name="DATADIVERS_PASSWORD_SANDBOX" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DATADIVERS_USERNAME</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DATADIVERS_USERNAME') }}" name="DATADIVERS_USERNAME" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DATADIVERS_PASSWORD</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DATADIVERS_PASSWORD') }}" name="DATADIVERS_PASSWORD" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">INFUTOR_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('INFUTOR_USER') }}" name="INFUTOR_USER" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">INFUTOR_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('INFUTOR_PASS') }}" name="INFUTOR_PASS" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">INFUTOR_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('INFUTOR_URL') }}" name="INFUTOR_URL" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">INFUTOR_AUTO_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('INFUTOR_AUTO_URL') }}" name="INFUTOR_AUTO_URL" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">USINFO_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('USINFO_USER') }}" name="USINFO_USER" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">USINFO_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('USINFO_PASS') }}" name="USINFO_PASS" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">USINFO_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('USINFO_URL') }}" name="USINFO_URL" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">NEEYAMO_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('NEEYAMO_USER') }}" name="NEEYAMO_USER" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">NEEYAMO_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('NEEYAMO_PASS') }}" name="NEEYAMO_PASS" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">NEEYAMO_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('NEEYAMO_URL') }}" name="NEEYAMO_URL" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_USER') }}" name="DENSPRI_USER" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_PASS') }}" name="DENSPRI_PASS" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_URL') }}" name="DENSPRI_URL" type="text">
				  </div>
				</div>
				
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_TEST_USER</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_TEST_USER') }}" name="DENSPRI_TEST_USER" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_TEST_PASS</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_TEST_PASS') }}" name="DENSPRI_TEST_PASS" type="text">
				  </div>
				</div>
				<div class="form-group row">
					<label class="col-3 col-form-label">DENSPRI_TEST_URL</label>
					<div class="col-md-8">
				      <input class="form-control" value="{{ env('DENSPRI_TEST_URL') }}" name="DENSPRI_TEST_URL" type="text">
				  </div>
				</div>
				
				
				{!! Form::submit('Save Configurations', ['class' => 'btn btn-primary btn-lg']) !!}
				{!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
/*
	$(function() {
		$("form")[0].reset();
	});	
	*/
</script>

@endsection

