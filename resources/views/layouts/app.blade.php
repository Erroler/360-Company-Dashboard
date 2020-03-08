<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <title>@yield('title', 'VidalTech Dashboard')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/r-2.2.3/sc-2.0.1/datatables.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/tabler.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="/images/logo_icon.png">

    @yield('additional_headers')
  </head>
  <body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/r-2.2.3/sc-2.0.1/datatables.min.js"></script>
    @yield('content')
    <script src="{{ asset('js/tabler.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('additional_scripts')
  </body>
</html>