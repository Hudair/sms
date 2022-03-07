@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
@endsection

@section('content')
    <section class="row flexbox-container">
        <div class="col-xl-8 col-10 d-flex justify-content-center">
            <div class="card bg-authentication rounded-0 mb-0">
                <div class="row m-0">
                    <div class="col-lg-6 d-lg-block d-none text-center align-self-center pl-0 pr-3 py-0">
                        <img src="{{ asset('images/pages/register.jpg') }}" alt="branding logo">
                    </div>
                    <div class="col-lg-6 col-12 p-0">
                        <div class="card rounded-0 mb-0 p-2">
                            <div class="card-header pt-50 pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">{{ __('locale.auth.create_account') }}</h4>
                                </div>
                            </div>
                            <p class="px-2">{{ __('locale.auth.create_new_account') }}.</p>
                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="form-label-group">
                                            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="{{ __('locale.labels.first_name') }}" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>
                                            <label for="first_name" class="required">{{ __('locale.labels.first_name') }}</label>
                                            @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror


                                            @if(config('no-captcha.registration'))
                                                @error('g-recaptcha-response')
                                                <span class="text-danger">{{ __('locale.labels.g-recaptcha-response') }}</span>
                                                @enderror
                                            @endif

                                        </div>

                                        <div class="form-label-group">
                                            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder="{{ __('locale.labels.last_name') }}" value="{{ old('last_name') }}" autocomplete="last_name">
                                            <label for="last_name" class="required">{{ __('locale.labels.last_name') }}</label>
                                            @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>


                                        <div class="form-label-group">
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="{{ __('locale.labels.email') }}" value="{{ old('email') }}" required autocomplete="email">
                                            <label for="email">{{ __('locale.labels.email') }}</label>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-label-group show_hide_password">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('locale.labels.password') }}" required autocomplete="new-password">
                                            <label for="password">{{ __('locale.labels.password') }}</label>
                                            <div class="form-control-position cursor-pointer">
                                                <i class="feather icon-eye-off"></i>
                                            </div>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                        <div class="form-label-group show_hide_password">
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('locale.labels.password_confirmation') }}" required autocomplete="new-password">
                                            <label for="password-confirm">{{ __('locale.labels.password_confirmation') }}</label>
                                            <div class="form-control-position cursor-pointer">
                                                <i class="feather icon-eye-off"></i>
                                            </div>
                                        </div>

                                        @if(config('no-captcha.registration'))
                                            <fieldset class="form-label-group position-relative">
                                                {{ no_captcha()->input('g-recaptcha-response') }}
                                            </fieldset>
                                        @endif

                                        <a href="{{url('login')}}" class="btn btn-outline-primary float-left btn-inline mb-50">{{ __('locale.auth.login') }}</a>
                                        <button type="submit" class="btn btn-primary float-right btn-inline mb-50">{{ __('locale.auth.register') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if(config('no-captcha.registration'))
    @push('scripts')
        {{ no_captcha()->script() }}
        {{ no_captcha()->getApiScript() }}

        <script>
            grecaptcha.ready(() => {
                window.noCaptcha.render('register', (token) => {
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

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

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

