<!doctype html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Crowdin in context translation -->
    @if ( app('request')->input('translate') )
        <script type="text/javascript">
            var _jipt = [];
            _jipt.push(['project', 'imagery']);
        </script>
        <script type="text/javascript" src="//cdn.crowdin.com/jipt/jipt.js"></script>
        <script>var crowdin_lang = 'zu';</script>
    @endif

    <!-- Scripts -->
    @if( Auth::user() && ! Auth::user()->pending_approval )
        <script src="{{ mix('js/app.js') }}" defer></script>
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    @auth
        <style>
            @font-face {
                font-family: 'SanukFat';
                src: url('{{ route('fonts', 'SanukOT-Fat.otf') }}');
            }

            @font-face {
                font-family: 'SanukBold';
                src: url('{{ route('fonts', 'SanukOT-Bold.otf') }}');
            }
        </style>
    @endauth

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/material-icons.css') }}" rel="stylesheet">
    <link href="{{ mix('css/VueSearchSelect.css') }}" rel="stylesheet">
    <link href="{{ mix('css/vue2-animate.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png?v=RyxBBgpl7J">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png?v=RyxBBgpl7J">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png?v=RyxBBgpl7J">
    <link rel="manifest" href="/favicon/site.webmanifest?v=RyxBBgpl7J">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg?v=RyxBBgpl7J" color="#84b414">
    <link rel="shortcut icon" href="/favicon/favicon.ico?v=RyxBBgpl7J">
    <meta name="apple-mobile-web-app-title" content="cd.gruene.ch">
    <meta name="application-name" content="cd.gruene.ch">
    <meta name="msapplication-TileColor" content="#84b414">
    <meta name="msapplication-config" content="/favicon/browserconfig.xml?v=RyxBBgpl7J">
    <meta name="theme-color" content="#ffffff">

</head>
<body>
<div id="content" class="container-fluid">
    @yield('content')
</div>
</body>
</html>
