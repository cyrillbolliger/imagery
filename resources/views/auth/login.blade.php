@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 d-none d-md-block">
                <img
                    src="/images/home-{{ substr($lang, 0, 2) }}.jpeg"
                    alt="{{__('Design green images with ease.')}}"
                    class="img-fluid">
            </div>
            <div class="col-md-6 col-lg-4">
                <div>
                    <h2>{{ __('Login') }}</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        @error('email')
                        <div class="alert alert-warning" role="alert">
                            <strong>{{ $message }}</strong>
                            {{__('Would you like to reset your password?')}}
                            <a href="{{ route('password.request') }}">{{ __('Reset password.')  }}</a>
                            {{__("Or haven't you got an account yet?")}}
                            <a href="{{ route('register') }}">{{ __('Create account.') }}</a>
                        </div>
                        @enderror

                        <div class="form-group">
                            <label for="email"
                                   class="col-form-label">{{ __('E-Mail Address') }}</label>

                            <input id="email"
                                   type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="user@example.com"
                                   required
                                   autocomplete="email"
                                   autofocus>
                        </div>

                        <div class="form-group">
                            <label for="password"
                                   class="col-form-label">{{ __('Password') }}</label>

                            <input id="password"
                                   type="password"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="password"
                                   placeholder="Password"
                                   required
                                   autocomplete="current-password">

                            <small id="passwordHelpBlock" class="form-text text-muted">
                                {{ __('Forgot password?') }}
                                <a href="{{ route('password.request') }}">
                                    {{ __('Reset it!')  }}
                                </a>
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember"
                                       id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Stay logged in') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0 mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>

                            <a href="{{ route('register') }}" class="btn btn-link">{{ __('Create account') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
