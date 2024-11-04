<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Pet Food Reviews')</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/wp.css') }}">
  </head>
  <body>  
  @include('layouts.navbar')
  <div class="container">
    @yield('content')
  </div>
  @include('layouts.footer')
</body>
</html>