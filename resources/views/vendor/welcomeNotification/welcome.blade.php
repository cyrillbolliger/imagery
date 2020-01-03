@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 pb-5">
            <div class="col-md-6 pt-5 pb-3">
                <img
                    src="/images/home-{{ substr($lang, 0, 2) }}.jpeg"
                    alt="{{__('Design green images with ease.')}}"
                    class="img-fluid">
            </div>
            <div class="col-md-6 col-lg-4">
                <h1>{{ __('Welcome :firstName!', ['firstName' => $user->first_name]) }}</h1>
                <p>
                    {{ __('A tool to create beautiful images is just one step away.') }}
                    <strong>{{ __('Set yourself a password and your ready to go!') }}</strong>
                </p>

                <form method="POST">
                    @csrf

                    <input type="hidden" name="email" value="{{ $user->email }}"/>

                    <div class="form-group">
                        <label for="password" class="col-form-label">{{ __('Password') }}</label>

                        <input id="password"
                               type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="new-password"
                               autofocus>

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="col-form-label">{{ __('Confirm Password') }}</label>

                        <input id="password-confirm"
                               type="password"
                               class="form-control"
                               name="password_confirmation"
                               required
                               autocomplete="new-password">
                    </div>

                    <div class="form-group mb-0 mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save password and login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
