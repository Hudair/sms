@extends('layouts/fullLayoutMaster')

@section('title', __('locale.labels.subscribe'))

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
                        <img src="{{ asset('images/pages/reset-password.png') }}" alt="branding logo">
                    </div>
                    <div class="col-lg-6 col-12 p-0">
                        <div class="card rounded-0 mb-0 p-2">
                            <div class="card-header pt-50 pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">{{ __('locale.labels.subscribe') }}</h4>
                                </div>
                            </div>

                            <p class="px-2">{{ __('locale.labels.welcome_to') }} {{ $contact->name }}</p>

                            <div class="card-content">
                                <div class="card-body pt-0">
                                    <form method="POST" action="{{ route('contacts.subscribe_url', $contact->uid) }}">
                                        @csrf

                                        <div class="form-label-group required">
                                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="{{ __('locale.labels.phone') }}" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                                            <label for="phone" class="required">{{ __('locale.labels.phone') }}</label>
                                            @error('phone')
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
                                            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="{{ __('locale.labels.first_name') }}" value="{{ old('first_name') }}" autocomplete="first_name">
                                            <label for="first_name" class="required">{{ __('locale.labels.first_name') }}</label>
                                            @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror

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

                                        @if(config('no-captcha.registration'))
                                            <fieldset class="form-label-group position-relative">
                                                {{ no_captcha()->input('g-recaptcha-response') }}
                                            </fieldset>
                                        @endif

                                        <button type="submit" class="btn btn-primary btn-block mb-50">{{ __('locale.labels.subscribe') }}</button>
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

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

    </script>
@endpush

