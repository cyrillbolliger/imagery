@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 pt-5 pb-5">
            <div class="col-md-6 col-lg-5">
                <h1>{{ __('Register') }}</h1>
                <p>{{ __('Please fill out this form to apply for a login. As we will review your application manually, it may take some time.') }}</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label for="first_name" class="col-form-label">{{ __('First Name') }}</label>

                        <input id="first_name"
                               type="text"
                               class="form-control @error('first_name') is-invalid @enderror"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               required
                               autocomplete="given-name"
                               autofocus>

                        @error('first_name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="col-form-label">{{ __('Last Name') }}</label>

                        <input id="last_name"
                               type="text"
                               class="form-control @error('last_name') is-invalid @enderror"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               required
                               autocomplete="family-name"
                               autofocus>

                        @error('last_name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city" class="col-form-label">{{ __('City') }}</label>

                        <input id="city"
                               type="text"
                               class="form-control @error('city') is-invalid @enderror"
                               name="city"
                               value="{{ old('city') }}"
                               required
                               autocomplete="address-level2"
                               autofocus>

                        @error('city')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email"
                               class="col-form-label">{{ __('E-Mail Address') }}</label>

                        <input id="email"
                               type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email">

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comment"
                               class="col-form-label">{{ __('Comment') }}</label>

                        <textarea id="comment"
                                  class="form-control"
                                  name="comment"
                                  placeholder="optional"
                                  rows="3"
                                  autocomplete="comment">{{ old('comment') }}</textarea>
                    </div>

                    <div class="form-group mb-0 mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Apply for a login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
