<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    @auth
        <link rel="stylesheet" href="{{ route('fonts', 'SanukOT-Bold.otf') }}" as="font" type="font/otf" crossorigin>
        <link rel="stylesheet" href="{{ route('fonts', 'SanukOT-Fat.otf') }}" as="font" type="font/otf" crossorigin>
@endauth

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="content" class="container-fluid">
    @yield('content')
</div>
</body>
</html>
