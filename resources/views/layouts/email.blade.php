<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">



    @yield('css')
</head>

<body>
    <div class="well">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h1 class="text-center">
                        @yield('title')
                    </h1>

                    @yield('before_content')

                    
                            <div class="panel">
                                <div class="panel-body">
                                    @yield('content')
                                </div>
                            </div>
                        

                    @yield('after_content')


                    <div class="row">
                        <div class="col-xs-6">
                            <p class="text-muted">
                                @yield('footer_left')
                            </p>
                        </div>
                        <div class="col-xs-6">
                            <p class="text-right text-muted">
                                @yield('footer_right')
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    @yield('footer')
            
                </div>
            </div>
        </div>
    </div>
</body>
</html>
