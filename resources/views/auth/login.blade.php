@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 d-none d-md-block">
                <img
                    src="/images/home-{{ substr($lang, 0, 2) }}.jpeg"
                    alt="{{__('Design GREEN images with ease.')}}"
                    class="img-fluid">
            </div>
            <div class="col-md-6 col-lg-4">
                <div>
                    <h2>{{ __('Login') }}</h2>

                    <p>{{ __( "This uses the same login as for other services of the GREENS like the chat." ) }}</p>

                    <a type="button"
                       class="btn btn-primary btn-lg btn-block mt-3 mb-3"
                       href="{{ route('keycloak.login') }}">{{__('Sign-on with GREEN account')}}</a>

                    <div class="text-muted small mt-4">
                        <h3>{{ __('FAQ') }}</h3>
                        <p>
                            <strong>{{ __( 'What about my existing account?' ) }}</strong><br>
                            {{ __( "If you already had an account for this tool, it will be automatically merged with the GREEN account." ) }}
                        </p>
                        <p>
                            <strong>{{ __( "I don't have a GREEN account (chat login)" ) }}</strong><br>
                            {{ __( "Then it's time you sign up for one." ) }}
                            <a href="{{ url('register') }}">{{ __( "Click here to register." ) }}</a>
                        </p>
                        <p>
                            <strong>{{ __( 'I need some help' ) }}</strong><br>
                            &rarr; <a href="mailto:{{ config('app.admin_email') }}">{{ config('app.admin_email') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
