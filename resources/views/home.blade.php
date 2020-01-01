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
                        <span class="sr-only">{{__('Loading...')}}</span>
                    </div>
                    <p class="mt-2">{{__('Loading...')}}</p>
                </div>
            </div>
        </div>

        <noscript>
            <h1>{{ __('Javascript is required') }}</h1>
            <p>
                {{__('Javascript is absolutely essential for this app to run.')}}
                <a href="https://www.enable-javascript.com/{{ substr($lang, 0, 2) }}" target="_blank">
                    {{__('Here is how you can enable it.')}}
                </a>
            </p>
        </noscript>

        <script>
            document.querySelector('.loader').classList.remove('d-none');
        </script>
    </div>
@endsection
