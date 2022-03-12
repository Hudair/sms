@php
    $configData = Helper::applClasses();
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.password_reset'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')

    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="{{route('login')}}">
                <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}"/>
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    @if($configData['theme'] === 'dark')
                        <img src="{{asset('images/pages/reset-password-v2-dark.svg')}}" class="img-fluid" alt="Register V2"/>
                    @else
                        <img src="{{asset('images/pages/reset-password-v2.svg')}}" class="img-fluid" alt="Register V2"/>
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Reset password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{ __('locale.auth.password_reset') }}</h2>
                    <p class="card-text mb-2">{{ __('locale.auth.enter_new_password') }}</p>
                    <form class="auth-reset-password-form mt-2" method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <div class="mb-1">
                            <label class="form-label" for="email">{{ __('locale.labels.email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('locale.labels.email') }}" value="{{ $email ?? old('email') }}" required autocomplete="email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">{{ __('locale.labels.new_password') }}</label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input id="password" type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" name="password" placeholder="{{ __('locale.labels.password') }}" required autocomplete="new-password" autofocus tabindex="1">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>

                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password-confirm">{{ __('locale.labels.password_confirmation') }}</label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input id="password-confirm" type="password" class="form-control form-control-merge" name="password_confirmation" placeholder="{{ __('locale.labels.password_confirmation') }}" required autocomplete="new-password">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>


                        @if(config('no-captcha.login'))
                            <div class="mb-1">
                                {{ no_captcha()->input('g-recaptcha-response') }}
                            </div>
                        @endif
                        <input type="hidden" name="token" value="{{ $token }}">
                        <button type="submit" class="btn btn-primary w-100" tabindex="3">{{ __('locale.buttons.reset') }}</button>
                    </form>
                    <p class="text-center mt-2">
                        <a href="{{url('login')}}">
                            <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Reset password-->
        </div>
    </div>
@endsection


@if(config('no-captcha.login'))
    @push('scripts')
        {{ no_captcha()->script() }}
        {{ no_captcha()->getApiScript() }}

        <script>
            grecaptcha.ready(() => {
                window.noCaptcha.render('login', (token) => {
                    document.querySelector('#g-recaptcha-response').value = token;
                });
            });
        </script>
    @endpush
@endif

