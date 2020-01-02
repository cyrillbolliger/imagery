@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <h1>{{ __('Thank you!') }}</h1>
                <p class="alert alert-success" role="alert">
                    {{ __("We've received your registration and we'll approve it as soon as possible. Since this is done manually, it may take a little while. We'll send you an email, as soon as your account is ready.") }}
                </p>
            </div>
        </div>
    </div>
@endsection
