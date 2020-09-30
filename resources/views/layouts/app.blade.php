<!DOCTYPE html>
<html>

<!--

User Information
_____________________________________________
User ID: {{ Auth::id() }}
Company: {{ Auth::user()->company }}
Name: {{ Auth::user()->full_name }}
Email: {{ Auth::user()->email }}
Phone: {{ displayPhone(Auth::user()->phone) }}
Authorization: Bearer {{ JWTAuth::fromUser(Auth::user()) }}

-->

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
      @if (isset($title))
        {{ $title . ' - ' . env('APP_NAME') }}
      @else
        {{ env('APP_NAME') }}
      @endif
    </title>
    
    <!-- stylesheets -->
    {!! Minify::stylesheet([
      secure_url('/css/vendor/bootstrap-3.3.7.min.css'),
      secure_url('/css/vendor/font-awesome.css'),
      secure_url('/css/vendor/ionicons.min.css'),
      secure_url('/css/vendor/AdminLTE.min.css'),
      secure_url('/css/vendor/skin-black.min.css'),
      secure_url('/css/vendor/skin-black-light.min.css'),
      secure_url('/css/vendor/skin-blue.min.css'),
      secure_url('/css/vendor/skin-blue-light.min.css'),
      secure_url('/css/vendor/skin-red.min.css'),
      secure_url('/css/vendor/skin-red-light.min.css'),
      secure_url('/css/vendor/skin-purple.min.css'),
      secure_url('/css/vendor/skin-purple-light.min.css'),
      secure_url('/css/vendor/skin-green.min.css'),
      secure_url('/css/vendor/skin-green-light.min.css'),
      secure_url('/css/vendor/skin-yellow.min.css'),
      secure_url('/css/vendor/skin-yellow-light.min.css'),
      secure_url('/css/vendor/animate.css'),
      secure_url('/css/vendor/jasny-bootstrap.min.css'),
      secure_url('/css/vendor/sweetalert.css'),
      secure_url('/css/vendor/bootstrap-toggle.min.css'),
      secure_url('/css/vendor/bootstrap-select.min.css'),
      secure_url('/css/vendor/jquery-ui.min.css'),
      secure_url('/css/custom.css'),
    ]) !!}

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link rel="icon" type="image/png" href="{{ secure_url('images/favicon.png') }}" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ secure_url('images/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ secure_url('images/favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ secure_url('images/favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ secure_url('images/favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ secure_url('images/favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ secure_url('images/favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ secure_url('images/favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ secure_url('images/favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_url('images/favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ secure_url('images/favicons/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_url('images/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ secure_url('images/favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_url('images/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_url('images/favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ secure_url('images/favicons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">


    @yield('css')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

</head>

<body class="hold-transition 
    
    @if ($whitelabel AND in_array($whitelabel->skin, [
        'black',
        'black-light',
        'blue',
        'blue-light',
        'red',
        'red-light',
        'purple',
        'purple-light',
        'green',
        'green-light',
        'yellow',
        'yellow-light',
    ]))
        skin-{{ $whitelabel->skin }}
    @else 
        skin-blue 
    @endif 
    sidebar-mini @if (!Auth::user()->is_sidebar) sidebar-collapse @endif">
    
    <input type="hidden" id="basePath" value="{{ secure_url('/') }}">
    
    <div class="wrapper">

        <header class="main-header">

            <!-- Logo -->
            <a href="{{ secure_url('/') }}" class="logo">
                
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">
                    @if ($whitelabel)
                        <img src="{{ secure_url($whitelabel->path.'images/logos/app.png') }}" class="img-responsive center-block" alt="logo">
                    @else
                        <img src="{{ secure_url('images/logos/block-200x127.jpg') }}" class="img-responsive center-block" alt="logo">
                    @endif
                </span>
            </a>

            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                	
                	<ul class="nav navbar-nav">
                		<div class="header_phone text-info">Phone: 1-800-203-4731</div>
                	</ul>

	                		
					{{-- 
                    <ul class="nav navbar-nav">
                        @if (!str_contains(url()->current(), 'admin') )
                            <li id="review-invite-link">
                                <a class="label-warning" href="{{ secure_url('checks/create') }}">
                                    <i class="fa fa-search-plus" aria-hidden="true"></i>
                                    &nbsp;Run a Check
                                </a>
                            </li>
                        @endif
                    </ul>
                    --}}
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                
                <!-- /.search form -->
                <ul class="sidebar-menu">
                    
                    <li class="header">{{ Auth::user()->full_name }}
                    	<br>Company ID: {{ Auth::user()->company_id }}
                    	@if(auth()->user()->sandbox)
                    	  <br><span style="color:#a94442;">Sandbox Mode</span>
                    	@endif
                    	@if(auth()->user()->company_rep)
                    	  <br><span style="color:#f39c12;">Authorized Rep</span>
                    	@endif
                    </li>
                    
                    @if(!Auth::user()->hasRole('limited_admin'))
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'reports') active @endif">
                        <a href="{{ secure_url('reports') }}">
                            <i class="fa fa-dashboard" aria-hidden="true"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(Auth::user()->company_rep && !Auth::user()->hasRole('limited_admin'))
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'transactions') active @endif">
                        <a href="{{ secure_url('transactions') }}">
                            <i class="fa fa-money" aria-hidden="true"></i> <span>Transactions</span>
                        </a>
                    </li>
                    @endif
                    
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'create_check') active @endif">
                        <a href="{{ secure_url('checks/create') }}">
                            <i class="fa fa-search-plus" aria-hidden="true"></i> <span>Run a Check</span>
                        </a>
                    </li>
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'checks') active @endif">
                        <a href="{{ secure_url('checks') }}">
                            <i class="fa fa-history" aria-hidden="true"></i> <span>History</span>
                        </a>
                    </li>
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'forms') active @endif">
                        <a href="{{ secure_url('forms') }}">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span>Forms</span>
                        </a>
                    </li>

                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'settings') active @endif">
                        <a href="{{ secure_url('settings') }}">
                            <i class="fa fa-cogs" aria-hidden="true"></i> <span>Settings</span>
                        </a>
                    </li>
                    
                    @if(!Auth::user()->hasRole('limited_admin'))
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'old_site') active @endif">
                        <a href="https://www.eyeforsecurity.com/admin/oldlogin.php">
                            <i class="fa fa-external-link" aria-hidden="true"></i> <span>View Records on Old Site</span>
                        </a>
                    </li>
					@endif
                    
                    
                    @if(Auth::user()->company_rep && Auth::user()->is_approved && !Auth::user()->hasRole('limited_admin'))
                    	<li class="@if(isset($active_menu_item) AND $active_menu_item == 'users_company') active @endif">
                        	<a href="{{ secure_url('users/company') }}">
                            	<i class="fa fa-users" aria-hidden="true"></i> <span>Authorized Users</span>
                        	</a>
                    	</li>
                    @endif
                    
                    @if( (Auth::user()->is_approved && Auth::user()->hasRole('limited_admin')))
                    	<li class="@if(isset($active_menu_item) AND $active_menu_item == 'limited_admin') active @endif">
                        	<a href="#" disabled>
                        		<span onclick="Admin.limitedAdminCompanyReport({{ Auth::user()->id}})">
                        			<i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Company Report</span>
                        		</span>
                        	</a>
                    	</li>
                    @endif
                    
                    @if(Session::has('disguised_user_id'))
                        <li>
                            <a href="{{ secure_url('undisguise') }}">
                                <i class="fa fa-user-secret" aria-hidden="true"></i> <span>Remove Disguise</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ secure_url('logout') }}">
                                <i class="fa fa-sign-out" aria-hidden="true"></i> <span>Logout</span>
                            </a>
                        </li>
                    @endif
                    
                    <!--/li-->

                    @if (Auth::user()->hasRole('admin'))
                        <li class="header">Admin</li>
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_dashboard') active @endif">
                            <a href="{{ secure_url('admin/reports') }}">
                                <i class="fa fa-dashboard" aria-hidden="true"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_users') active @endif">
                            <a href="{{ secure_url('admin/users') }}">
                                <i class="fa fa-users" aria-hidden="true"></i> <span>Users ( {{ \App\Models\User::count() }} )</span>
                            </a>
                        </li>
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_checks') active @endif">
                            <a href="{{ secure_url('admin/checks') }}">
                                <i class="fa fa-search" aria-hidden="true"></i> <span>Checks</span>
                            </a>
                        </li>
                        
                        {{-- 
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_diagnostics') active @endif">
                            <a href="{{ secure_url('admin/diagnostics') }}">
                                <i class="fa fa-sliders" aria-hidden="true"></i> <span>Diagnostics</span>
                            </a>
                        </li>
                        --}}
                        
                        
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_utilities') active @endif">
                            <a href="{{ secure_url('admin/utilities') }}">
                                <i class="fa fa-wrench" aria-hidden="true"></i> <span>Utilities</span>
                            </a>
                        </li>
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_pricing') active @endif">
                            <a href="{{ secure_url('admin/pricing') }}">
                                <i class="fa fa-usd" aria-hidden="true"></i> <span>Pricing</span>
                            </a>
                        </li>
                        
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'admin_configurations') active @endif">
                            <a href="{{ secure_url('admin/settings/configs') }}">
                                <i class="fa fa-cog" aria-hidden="true"></i><span>Configurations</span>
                            </a>
                        </li>
                        
                        <li class="@if(isset($active_menu_item) AND $active_menu_item == 'invoice_management') active @endif">
                            <a href="{{ secure_url('admin/invoices/manage') }}">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <span>Invoice Management</span>
                            </a>
                        </li>
                        
                        {{-- 
                        <li class="">
                            <a href="#" disabled>
                            	
                            	@php
                            	  $tz = date_default_timezone_get();
                            	  $info = getdate();
                            	  $d = $info['mon'] . "/" . $info['mday'] . "/" . $info["year"];
                            	@endphp
                            	
                                <i class="fa fa-clock-o" aria-hidden="true"></i><span class="system-datetime">{{ $d . "  " . $tz }}</span>
                            </a>
                        </li>
                        --}}
                        
                    @endif

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="fadein" style="display:none;">
                <section class="content">
                    @if (Auth::user()->is_suspended AND isset($active_menu_item) AND $active_menu_item != 'settings_payment')
                        <div class="alert alert-danger">
                            <h3>Account Suspended</h3>
                            <p>Your account has been temporarily suspended because of a past due balanace. No emails nor text messages will be sent on your behalf until your account is reinstated.</p>

                            <p>To Reinstate your account, just update your payment method by <a href="{{ secure_url('settings/payment') }}">clicking here</a>.</p>

                        </div>
                    @endif

                    @include('flash::message')
                    @if(count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </section>
            </div>
        </div>
    </div>
    
    

    <div class="modal-wrapper"></div>

    <div class="alert alert-danger" style="display:none;">
      <span class="message"></span>
    </div>
    
    <div class="progress-overlay">
    	<div class="progress-column">
    		<div class="progress-indicator">
    			
    			<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
    			<p class="progress-message">
    				Please wait....
    			</p>
    			
    		</div>
    	</div>
    </div>
    
    <style>
    	.progress-overlay{
    		position: absolute;
    		top: 0;
    		left: 0;
    		z-index: 10000;
    		background-color: rgba(200,200,200, .5);
    		width: 100%;
    		height: 100%;
    		display: none;
    	}
    	
    	.progress-column{
    		width: 400px;
    		margin: 0 auto;
    		height: 100%;
    		display: flex;
			align-items: center;
			justify-content: center;
    	}
    	
    	.progress-indicator{
    		width: 100%;
    		height: 200px;
    		font-size: 20px;
    		text-align: center;
    	}
    </style>
    


    <!-- javascript -->
    {!! Minify::javascript([
      secure_url('/js/vendor/jquery-3.2.1.min.js'),
      secure_url('/js/vendor/jquery-ui.min.js'),  
      secure_url('/js/vendor/bootstrap-3.3.7.min.js'),
      secure_url('/js/vendor/jquery.slimscroll.min.js'),
      secure_url('/js/vendor/jquery.highlight.js'),
      secure_url('/js/vendor/fastclick.js'),
      secure_url('/js/vendor/moment.js'),
      secure_url('/js/vendor/sweetalert.min.js'),
      secure_url('/js/vendor/jasny-bootstrap.min.js'),
      secure_url('/js/vendor/AdminLTE.min.js'),
      secure_url('/js/vendor/chart.min.js'),
      secure_url('/js/vendor/bootstrap-toggle.min.js'),
      secure_url('/js/vendor/jquery.playSound.js'),
      secure_url('/js/vendor/bootstrap-select.min.js'),
      secure_url('/js/custom.js'),
      secure_url('/js/utilities.js'),
      secure_url('/js/conversion.js'),
    ]) !!}
    

    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'Authorization': 'Bearer {{ JWTAuth::fromUser(Auth::user()) }}'
                }
            });
        });
        
    </script>

    @yield('js')
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-104848558-2"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	
	  gtag('config', 'UA-104848558-2');
	</script>


</body>
</html>
