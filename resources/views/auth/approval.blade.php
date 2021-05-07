@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <h1>{{ __('approval.waiting') }}</h1>
                <p class="alert alert-success" role="alert">
                    {{ __('approval.confirmation') }}
                    <br><br>
                    {{ __('approval.contact') }}
                    <a href="mailto:{{ config('app.admin_email') }}">{{ config('app.admin_email') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
