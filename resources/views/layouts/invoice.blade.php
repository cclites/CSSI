<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="{{ public_path('css/vendor/bootstrap-3.3.7.min.css') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
  <style>
  	
  	table tr:nth-of-type(6),
  	table tr:nth-of-type(24){
  		page-break-after: always;
  	}
  	
  </style>
</head>
<body>
<div class="wrapper">
  
  @yield('content')

</div>
<!-- ./wrapper -->
</body>
</html>
