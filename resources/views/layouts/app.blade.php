<!doctype html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('includes.scripts')
    @include('includes.fonts')
    @include('includes.styles')
    @include('includes.favicon')
    @include('includes.og')
</head>
<body>
<div id="content" class="container-fluid">
    @yield('content')
</div>
</body>
</html>
