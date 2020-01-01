@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <h1>{{ __('Reset Password') }}</h1>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email"
                               class="col-form-label">{{ __('E-Mail Address') }}</label>

                        <input id="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ $email ?? old('email') }}"
                               placeholder="user@example.com"
                               required
                               autocomplete="email"
                               autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password"
                               class="col-form-label">{{ __('Password') }}</label>

                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm"
                               class="col-form-label">{{ __('Confirm Password') }}</label>

                        <input id="password-confirm"
                               type="password"
                               class="form-control"
                               name="password_confirmation"
                               placeholder="Password"
                               required
                               autocomplete="new-password">
                    </div>

                    <div class="form-group mb-0 mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
