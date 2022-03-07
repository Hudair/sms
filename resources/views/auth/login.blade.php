@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.login'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
@endsection

@section('content')
    <section class="row flexbox-container">
        <div class="col-xl-8 col-11 d-flex justify-content-center">
            <div class="card bg-authentication rounded-0 mb-0">
                <div class="row m-0">
                    <div class="col-lg-5 d-lg-block d-none text-center align-self-center px-1 py-0">
                        <img src="{{ asset('images/pages/reset-password.png') }}" alt="branding logo">
                    </div>
                    <div class="col-lg-7 col-12 p-0">
                        <div class="card rounded-0 mb-0 px-2">
                            <div class="card-header pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">{{ __('locale.auth.login') }}</h4>
                                </div>
                            </div>

                            <p class="px-2">{{ __('locale.auth.welcome_message') }}</p>

                            @if(config('app.env') == 'demo')
                                <p class="px-2" style="cursor: pointer;">
                                    <span class="text-primary admin-login">Admin Login</span>
                                    <span class="text-success pull-right client-login">Client Login</span>
                                </p>
                            @endif

                            <div class="card-content">
                                <div class="card-body pt-1">
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <fieldset class="form-label-group form-group position-relative">

                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('locale.labels.email') }}" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                            <label for="email">{{ __('locale.labels.email') }}</label>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <span>{{ $message }}</span>
                                            </span>
                                            @enderror

                                            @error('g-recaptcha-response')
                                            <span class="text-danger">{{ __('locale.labels.g-recaptcha-response') }}</span>
                                            @enderror
                                        </fieldset>

                                        <fieldset class="form-label-group position-relative show_hide_password">
                                            <input id="password"
                                                   type="password"
                                                   class="form-control"
                                                   name="password"
                                                   placeholder="{{__('locale.labels.password')}}"
                                                   required
                                                   autocomplete="password"
                                                   @if(config('app.env') == 'demo')
                                                   value="12345678"
                                                    @endif
                                            >
                                            <label for="password">{{__('locale.labels.password')}}</label>
                                            <div class="form-control-position cursor-pointer">
                                                <i class="feather icon-eye-off"></i>
                                            </div>
                                        </fieldset>


                                        @if(config('no-captcha.login'))

                                            <fieldset class="form-label-group position-relative">
                                                {{ no_captcha()->input('g-recaptcha-response') }}
                                            </fieldset>

                                        @endif

                                        <div class="form-group d-flex justify-content-between align-items-center">
                                            <div class="text-left">
                                                <fieldset class="checkbox">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" {{ old('remember') ? 'checked' : '' }} name="remember">
                                                        <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check">
                                                              <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                          </span>
                                                        <span class="">{{__('locale.auth.remember_me')}}</span>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <div class="text-right">
                                                    <a class="card-link" href="{{ route('password.request') }}">{{ __('locale.auth.forgot_password') }}?</a>
                                                </div>
                                            @endif

                                        </div>
                                        @if(config('account.can_register'))
                                            <a href="{{route('register')}}" class="btn btn-outline-primary float-left btn-inline">{{ __('locale.auth.register') }}</a>
                                        @endif
                                        <button type="submit" class="btn btn-primary float-right btn-inline">{{ __('locale.auth.login') }}</button>
                                    </form>
                                </div>
                            </div>

                            @if(config('services.facebook.active') || config('services.twitter.active') || config('services.google.active') || config('services.github.active'))
                                <div class="login-footer">
                                    <div class="divider">
                                        <div class="divider-text">OR</div>
                                    </div>
                                    <div class="footer-btn d-flex justify-content-between flex-wrap">
                                        @if(config('services.facebook.active'))
                                            <a href="{{route('social.login', 'facebook')}}" class="btn btn-flat-primary" data-toggle="tooltip" data-placement="top"
                                               title="Facebook"><span class="feather icon-facebook us-2x"></span></a>
                                        @endif

                                        @if(config('services.twitter.active'))
                                            <a href="{{route('social.login', 'twitter')}}" class="btn btn-flat-info" data-toggle="tooltip" data-placement="top"
                                               title="Twitter"><span class="feather icon-twitter us-2x"></span></a>
                                        @endif

                                        @if(config('services.google.active'))
                                            <a href="{{route('social.login', 'google')}}" class="btn btn-flat-danger" data-toggle="tooltip" data-placement="top"
                                               title="Google"><span class="feather icon-at-sign us-2x"></span></a>
                                        @endif

                                        @if(config('services.github.active'))
                                            <a href="{{route('social.login', 'github')}}" class="btn btn-flat-dark" data-toggle="tooltip" data-placement="top"
                                               title="Github"><span class="feather icon-github us-2x"></span></a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="m-2"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

        let firstInvalid = $('form').find('.is-invalid').eq(0);
        let showHideInput = $('.show_hide_password input');
        let showHideIcon = $('.show_hide_password i');

        $('.admin-login').on('click', function () {
            $('#email').val('admin@codeglen.com')
        });

        $('.client-login').on('click', function () {
            $('#email').val('client@codeglen.com')
        });

        $(".form-control-position").on('click', function (event) {
            event.preventDefault();
            if (showHideInput.attr("type") === "text") {
                showHideInput.attr('type', 'password');
                showHideIcon.addClass("icon-eye-off");
                showHideIcon.removeClass("icon-eye");
            } else if (showHideInput.attr("type") === "password") {
                showHideInput.attr('type', 'text');
                showHideIcon.removeClass("icon-eye-off");
                showHideIcon.addClass("icon-eye");
            }
        });
    </script>
@endpush
