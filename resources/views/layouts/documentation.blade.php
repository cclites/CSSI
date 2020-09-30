<!DOCTYPE html>
<html>

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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- stylesheets -->
    {!! Minify::stylesheet([
      secure_url('/css/vendor/bootstrap-3.3.7.min.css'),
      secure_url('/css/vendor/font-awesome.css'),
      secure_url('/css/vendor/ionicons.min.css'),
      secure_url('/css/vendor/select2.min.css'),
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
      secure_url('/css/vendor/selectize.bootstrap3.css'),
      secure_url('/css/custom.css'),
    ]) !!}

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

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

<body class="hold-transition skin-black-light sidebar-mini">
    <div class="wrapper">

        <header class="main-header">

            <!-- Logo -->
            <a href="{{ secure_url('/') }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>Doc</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">
                    <i class="fa fa-book" aria-hidden="true"></i>
                    <strong>Documentation</strong>
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
                        <li>
                            <a href="{{ secure_url('/') }}">
                                <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
                                <span class="">Back to App</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                
                <!-- /.search form -->
                <ul class="sidebar-menu">
                    
                    <li class="header">API Documentation</li>
                    
                    <li class="@if(isset($active_menu_item) AND $active_menu_item == 'documentation_home') active @endif">
                        <a href="{{ secure_url('documentation') }}">
                            <i class="fa fa-home" aria-hidden="true"></i> <span>Home</span>
                        </a>
                    </li>


                    <li>
                        <a href="{{ secure_url('/') }}">
                            <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> <span>Back to App</span>
                        </a>
                    </li>
                    

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="fadein" style="display:none;">
                <section class="content">
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


    <!-- Loading Modal -->
    <div class="modal fade" id="modal-loading" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="text-center">
                <span class="spacer"></span>
                <br>
                <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
            </div>
        </div>
    </div>




    <!-- javascript -->
    {!! Minify::javascript([
      secure_url('/js/vendor/bootstrap-3.3.7.min.js'),
      secure_url('/js/vendor/jquery.slimscroll.min.js'),
      secure_url('/js/vendor/jquery.highlight.js'),
      secure_url('/js/vendor/fastclick.js'),
      secure_url('/js/vendor/moment.js'),
      secure_url('/js/vendor/sweetalert.min.js'),
      secure_url('/js/vendor/select2.min.js'),
      secure_url('/js/vendor/jasny-bootstrap.min.js'),
      secure_url('/js/vendor/AdminLTE.min.js'),
      secure_url('/js/vendor/bootstrap-toggle.min.js'),
      secure_url('/js/vendor/jquery.playSound.js'),
      secure_url('/js/custom.js')
    ]) !!}


    @yield('js')

</body>
</html>
