<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>coachtechフリマ</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
  <link rel="stylesheet" href="{{ asset('css/common.css')}}">
  @yield('css')
</head>

<body>
  <div class="app">
    <header class="header">
      <img class="header__logo" src="{{ asset('images/logo.svg')}}">
      @yield('search')
      @yield('link')
    </header>
    <div class="content">
      @yield('content')
    </div>
  </div>

  @yield('scripts')
</body>

</html>