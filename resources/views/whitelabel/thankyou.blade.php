@extends('layouts.whitelabel')

@section('content');
  <h1 class="text-center">Thank You</h1>
  
  @php
    Auth::logout();
  @endphp
  
@endsection
