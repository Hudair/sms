@php
    $configData = Helper::applClasses();
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.login'))

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
                        <img class="img-fluid" src="{{asset('images/pages/login-v2-dark.svg')}}" alt="{{config('app.name')}}"/>
                    @else
                        <img class="img-fluid" src="{{asset('images/pages/login-v2.svg')}}" alt="{{config('app.name')}}"/>
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Login-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{ __('locale.labels.welcome_to') }} {{config('app.name')}}</h2>
                    <p class="card-text mb-2">{{__('locale.auth.welcome_message')}}</p>


                    @if(config('app.env') == 'demo')
                        <p class="px-2" style="cursor: pointer;">
                            <span class="text-primary admin-login">Admin Login</span>
                            <span class="text-success pull-right client-login">Client Login</span>
                        </p>
                    @endif

                    <form class="auth-login-form mt-2" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="email">{{ __('locale.labels.email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('locale.labels.email') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                                <div class="alert-body d-flex align-items-center">
                                    <i data-feather="info" class="me-50"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                            @enderror

                            @error('g-recaptcha-response')
                            <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                                <div class="alert-body d-flex align-items-center">
                                    <i data-feather="info" class="me-50"></i>
                                    <span>{{ __('locale.labels.g-recaptcha-response') }}</span>
                                </div>
                            </div>
                            @enderror
                        </div>


                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">{{__('locale.labels.password')}}</label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">
                                        <small>{{ __('locale.auth.forgot_password') }}?</small>
                                    </a>
                                @endif
                            </div>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input id="password" type="password" class="form-control" name="password" placeholder="{{__('locale.labels.password')}}"
                                       required autocomplete="password" @if(config('app.env') == 'demo') value="12345678" @endif>
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>

                        @if(config('no-captcha.login'))
                            <div class="mb-1">
                                {{ no_captcha()->input('g-recaptcha-response') }}
                            </div>
                        @endif


                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input" {{ old('remember') ? 'checked' : '' }} name="remember" id="remember-me" type="checkbox" tabindex="3"/>
                                <label class="form-check-label" for="remember-me"> {{__('locale.auth.remember_me')}}</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" tabindex="4">{{__('locale.auth.login')}}</button>
                    </form>

                    @if(config('account.can_register'))
                        <p class="text-center mt-2">
                            <span>{{__('locale.auth.new_on_our_platform')}}?</span>
                            <a href="{{route('register')}}"><span>&nbsp;{{__('locale.auth.register')}}</span></a>
                        </p>
                    @endif

                    @if(config('services.facebook.active') || config('services.twitter.active') || config('services.google.active') || config('services.github.active'))
                        <div class="divider my-2">
                            <div class="divider-text">{{__('locale.auth.or')}}</div>
                        </div>

                        <div class="auth-footer-btn d-flex justify-content-center">

                            @if(config('services.facebook.active'))
                                <a class="btn btn-facebook" href="{{route('social.login', 'facebook')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook">
                                    <i data-feather="facebook"></i>
                                </a>
                            @endif

                            @if(config('services.twitter.active'))
                                <a class="btn btn-twitter" href="{{route('social.login', 'twitter')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                    <i data-feather="twitter"></i>
                                </a>
                            @endif

                            @if(config('services.google.active'))
                                <a class="btn btn-google" href="{{route('social.login', 'google')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Google">
                                    <i data-feather="mail"></i>
                                </a>
                            @endif

                            @if(config('services.github.active'))
                                <a class="btn btn-github" href="{{route('social.login', 'github')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Github">
                                    <i data-feather="github"></i>
                                </a>
                            @endif

                        </div>
                    @endif


                </div>
            </div>
            <!-- /Login-->
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

@push('scripts')
    <script>
        $('.admin-login').on('click', function () {
            $('#email').val('admin@codeglen.com')
        });

        $('.client-login').on('click', function () {
            $('#email').val('client@codeglen.com')
        });
    </script>
@endpush
