@extends('layouts.app')

@section('content')
    <script>
        window.user = {!! json_encode(Auth::user()) !!}
    </script>
    <div id="app">
        <div class="loader d-none">
            <div class="d-flex justify-content-center align-items-center min-vh-100 min-vw-100">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">{{__('home.loading')}}</span>
                    </div>
                    <p class="mt-2">{{__('home.loading')}}</p>
                </div>
            </div>
        </div>

        <noscript>
            <h1>{{ __('home.jsTitle') }}</h1>
            <p>
                {{__('home.jsDesc')}}
                <a href="https://www.enable-javascript.com/{{ substr($lang, 0, 2) }}" target="_blank">
                    {{__('home.jsEnable')}}
                </a>
            </p>
        </noscript>

        <script>
            document.querySelector('.loader').classList.remove('d-none');
        </script>
    </div>
@endsection
