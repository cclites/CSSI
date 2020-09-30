@extends('layouts.app', [
	'title' => 'API',
    'active_menu_parent' => 'settings',
    'active_menu_item' => 'settings',
])


@section('content')

<?php



?>

<div class="row settings">
	
	<div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large text-info">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    API Settings
                </p>
                <p class="text-muted">
                    <strong>Account ID:</strong> {{ auth()->user()->id }}
                    <br>
                    <strong>Account Key:</strong> {{ auth()->user()->key }}
                </p>
                 
                @if( auth()->user()->hasApiRole() )
	                @if(auth()->user()->sandbox)
	               
	                <!--p>
	                    <button onclick="Settings.toggleSandboxOff();" class="btn btn-lg btn-danger">Disable Sandbox</button>
	                </p-->
	                @else
	                
	                <!--p>
	                    <button onclick="Settings.toggleSandboxOn();" class="btn btn-lg btn-warning">Enable Sandbox</button>
	                </p-->
	                @endif
	            @endif
	            
            </div>
        </div>
    </div>
    

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <h1 class="text-large text-yellow">
                    <i class="fa fa-cloud" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    API Token
                </p>
                <p class="text-muted">
                    {{  JWTAuth::fromUser(auth()->user()) }}
                </p>
            </div>
        </div>
    </div>

    

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-body text-center">
            	
                <h1 class="text-large text-danger">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </h1>
                <p class="lead">
                    Learn how to interact with our RESTful API and receive JSON responses.
                </p>
                
                
                	
                <div class="list-group">
                	
                	<a href="{{ secure_url('files/Api/CSSI-Home-Auto.docx') }}" target="_blank" class="list-group-item">
						<p class="list-group-item-text">
							<strong>Home and Auto</strong>
						</p>
					</a>
                	
                	<a href="{{ secure_url('files/Api/Cssi-Personal.docx') }}" target="_blank" class="list-group-item">
						<p class="list-group-item-text">
							<strong>Personal</strong>
						</p>
					</a>
					
					<a href="{{ secure_url('files/Api/MVR.docx') }}" target="_blank" class="list-group-item">
						<p class="list-group-item-text">
							<strong>MVR</strong>
						</p>
					</a>
					<a href="{{ secure_url('files/Api/Single-Eye.docx') }}" target="_blank" class="list-group-item">
						<p class="list-group-item-text">
							<strong>National Single-Eye</strong>
						</p>
					</a>
					
					
					<!--a href="{{ secure_url('files/Tri-Eye.docx') }}" target="_blank" class="list-group-item">
						<p class="list-group-item-text">
							<strong>National Tri-Eye</strong>
						</p>
					</a-->
					
				</div>
                

            </div>
        </div>
    </div>

</div>
@endsection