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

</head>
<body>
<div id="content" class="container-fluid">
    @yield('content')
</div>
</body>
</html>
