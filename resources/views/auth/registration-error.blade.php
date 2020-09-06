@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 pt-5 pb-5">
            <div class="col-md-6 col-lg-5">
                <h1>{{ __('Sorry.') }}</h1>
                <p>{{ __('Something went wrong with your registration. Please contact us.') }}
                &rarr; <a href="mailto:{{ config('app.admin_email') }}">{{ config('app.admin_email') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
