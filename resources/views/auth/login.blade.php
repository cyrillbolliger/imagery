@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 d-none d-md-block">
                <img
                    src="/images/home-{{App::getLocale()}}.jpeg?"
                    alt="{{__('login.imgAlt')}}"
                    class="img-fluid">
            </div>
            <div class="col-md-6 col-lg-4">
                <div>
                    <h2>{{ __('login.login') }}</h2>

                    <p>{{ __( 'login.ssoInfo' ) }}</p>

                    <a type="button"
                       class="btn btn-primary btn-lg btn-block mt-3 mb-3"
                       href="{{ route('keycloak.login') }}">{{__('login.signOn')}}</a>

                    <div class="text-muted small mt-4">
                        <h3>{{ __('login.faq') }}</h3>
                        <p>
                            <strong>{{ __( 'login.existingTitle' ) }}</strong><br>
                            {{ __( 'login.existingDesc' ) }}
                        </p>
                        <p>
                            <strong>{{ __( 'login.noLoginTitle' ) }}</strong><br>
                            {{ __( 'login.noLoginDesc' ) }}
                            <a href="{{ url('register') }}">{{ __( 'login.register' ) }}</a>
                        </p>
                        <p>
                            <strong>{{ __( 'login.helpTitle' ) }}</strong><br>
                            &rarr; <a href="mailto:{{ config('app.admin_email') }}">{{ config('app.admin_email') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
