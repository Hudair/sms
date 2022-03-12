@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', __('locale.labels.subscribe'))

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
                        <img src="{{asset('images/pages/reset-password-v2-dark.svg')}}" class="img-fluid" alt="{{ config('app.name') }}"/>
                    @else
                        <img src="{{asset('images/pages/reset-password-v2.svg')}}" class="img-fluid" alt="{{ config('app.name') }}"/>
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Reset password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bold mb-1">{{ __('locale.labels.subscribe') }}</h2>
                    <p class="card-text mb-2">{{ __('locale.labels.welcome_to') }} {{ $contact->name }}</p>
                    <form method="POST" class="auth-reset-password-form mt-2" action="{{ route('contacts.subscribe_url', $contact->uid) }}">
                        @csrf
                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="phone">{{ __('locale.labels.phone') }}</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input id="phone" type="text" class="form-control form-control-merge @error('phone') is-invalid @enderror" name="phone" placeholder="{{ __('locale.labels.phone') }}" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                            </div>
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        @if(config('no-captcha.registration'))
                            @error('g-recaptcha-response')
                            <span class="text-danger">{{ __('locale.labels.g-recaptcha-response') }}</span>
                            @enderror
                        @endif


                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="first_name">{{ __('locale.labels.first_name') }}</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input id="first_name" type="text" class="form-control form-control-merge @error('first_name') is-invalid @enderror" name="first_name" placeholder="{{ __('locale.labels.first_name') }}" value="{{ old('first_name') }}" autocomplete="first_name">
                            </div>
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        <div class="mb-1">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="last_name">{{ __('locale.labels.last_name') }}</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input id="last_name" type="text" class="form-control form-control-merge @error('last_name') is-invalid @enderror" name="last_name" placeholder="{{ __('locale.labels.last_name') }}" value="{{ old('last_name') }}" autocomplete="last_name">
                            </div>
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        @if(config('no-captcha.registration'))
                            <fieldset class="form-label-group position-relative">
                                {{ no_captcha()->input('g-recaptcha-response') }}
                            </fieldset>
                        @endif

                        <button class="btn btn-primary w-100" type="submit" tabindex="3">{{ __('locale.labels.subscribe') }}</button>
                    </form>
                </div>
            </div>
            <!-- /Reset password-->
        </div>
    </div>
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

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

    </script>
@endpush
