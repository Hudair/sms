@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
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
            <div class="col-lg-3 d-none d-lg-flex align-items-center p-0">
                <div class="w-100 d-lg-flex align-items-center justify-content-center">
                    <img class="img-fluid w-100" src="{{asset('images/pages/create-account.svg')}}" alt="{{config('app.name')}}"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Register-->
            <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                <div class="width-700 mx-auto">
                    <div class="bs-stepper register-multi-steps-wizard shadow-none">
                        <div class="bs-stepper-header px-0" role="tablist">


                            <div class="step" data-target="#account-details" role="tab" id="account-details-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                      <i data-feather="home" class="font-medium-3"></i>
                                    </span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('locale.labels.account') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('locale.auth.enter_credentials') }}</span>
                                    </span>
                                </button>
                            </div>


                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>

                            <div class="step" data-target="#personal-info" role="tab" id="personal-info-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                        <i data-feather="user" class="font-medium-3"></i>
                                    </span>

                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('locale.auth.personal') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('locale.customer.personal_information') }}</span>
                                    </span>
                                </button>
                            </div>


                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>
                            <div class="step" data-target="#billing" role="tab" id="billing-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                      <i data-feather="credit-card" class="font-medium-3"></i>
                                    </span>

                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('locale.labels.billing') }}</span>
                                        <span class="bs-stepper-subtitle">{{ __('locale.labels.payment_details') }}</span>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="bs-stepper-content px-0 mt-4">

                            @if ($errors->any())

                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger" role="alert">
                                        <div class="alert-body">{{ $error }}</div>
                                    </div>
                                @endforeach

                            @endif


                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div id="account-details" class="content get_form_data" role="tabpanel" aria-labelledby="account-details-trigger">
                                    <div class="content-header mb-2">
                                        <h2 class="fw-bolder mb-75">{{ __('locale.auth.account_information') }}</h2>
                                        <span>{{ __('locale.auth.create_new_account') }}</span>
                                    </div>

                                    <div class="row">

                                        <div class="col-12 mb-1">
                                            <label class="form-label required" for="email">{{ __('locale.labels.email') }}</label>
                                            <input type="email" id="email" class="form-control required @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" required/>

                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-1">
                                            <label class="form-label required" for="password">{{ __('locale.labels.password') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password" required/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>

                                            @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-1">
                                            <label class="form-label required" for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                       value="{{ old('password_confirmation') }}"
                                                       name="password_confirmation" required/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>

                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="timezone">{{ __('locale.labels.timezone') }}</label>
                                            <select class="select2 w-100" name="timezone" id="timezone">
                                                @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                    <option value="{{$timezone['zone']}}" {{ config('app.timezone') == $timezone['zone'] ? 'selected': null }}>
                                                        {{ $timezone['text'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="locale">{{ __('locale.labels.language') }}</label>
                                            <select class="select2 w-100" name="locale" id="locale">
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->code }}" {{old('locale') == $language->code ? 'selected': null }}> {{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-1">

                                            @if(config('no-captcha.registration'))
                                                <fieldset class="form-label-group position-relative">
                                                    {{ no_captcha()->input('g-recaptcha-response') }}
                                                </fieldset>
                                            @endif

                                            @if(config('no-captcha.registration'))
                                                @error('g-recaptcha-response')
                                                <span class="text-danger">{{ __('locale.labels.g-recaptcha-response') }}</span>
                                                @enderror
                                            @endif
                                        </div>

                                        <p class="mt-2">
                                            <a href="{{url('login')}}">
                                                <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                                            </a>
                                        </p>

                                    </div>

                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-outline-secondary btn-prev" disabled type="button">
                                            <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                        </button>
                                        <button class="btn btn-primary btn-next" type="button">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                            <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="personal-info" class="content get_form_data" role="tabpanel" aria-labelledby="personal-info-trigger">
                                    <div class="content-header mb-2">
                                        <h2 class="fw-bolder mb-75">{{ __('locale.customer.personal_information') }}</h2>
                                        <span>{{ __('locale.auth.create_new_account') }}</span>
                                    </div>
                                    <div class="row">

                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="first_name">{{ __('locale.labels.first_name') }}</label>
                                            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="{{ __('locale.labels.first_name') }}" value="{{ old('first_name') }}" required autocomplete="first_name"/>
                                            @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror

                                        </div>


                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="last_name">{{ __('locale.labels.last_name') }}</label>
                                            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder="{{ __('locale.labels.last_name') }}" value="{{ old('last_name') }}" autocomplete="last_name"/>

                                            @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>


                                        <div class="col-md-6 mb-1">
                                            <label class="form-label required" for="phone">{{ __('locale.labels.phone') }}</label>
                                            <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" required placeholder="{{__('locale.labels.phone')}}" value="{{ old('phone') }}">

                                            @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-1">
                                            <label class="form-label" for="postcode">{{ __('locale.labels.postcode') }}</label>
                                            <input type="text" id="postcode" class="form-control @error('postcode') is-invalid @enderror" name="postcode" placeholder="{{__('locale.labels.postal_code')}}" value="{{ old('postcode') }}">
                                            @error('postcode')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-1">
                                            <label class="form-label required" for="address">{{ __('locale.labels.address') }}</label>
                                            <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" name="address" required placeholder="{{ __('locale.labels.address') }}" value="{{ old('address') }}">
                                            @error('address')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-1">
                                            <label class="form-label" for="company">{{ __('locale.labels.company') }}</label>
                                            <input type="text" id="company" class="form-control @error('company') is-invalid @enderror" name="company" placeholder="{{ __('locale.labels.company') }}" value="{{ old('company') }}">
                                            @error('company')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="city">{{ __('locale.labels.city') }}</label>
                                            <input type="text" id="city" class="form-control @error('city') is-invalid @enderror" name="city" required placeholder="{{ __('locale.labels.city') }}" value="{{ old('city') }}">
                                            @error('city')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="country">{{ __('locale.labels.country') }}</label>
                                            <select class="select2 w-100" name="country" id="country" required>
                                                @foreach(\App\Helpers\Helper::countries() as $country)
                                                    <option value="{{$country['name']}}" {{ config('app.country') == $country['name'] ? 'selected': null }}> {{ $country['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <p class="mt-1 mb-1">
                                        <a href="{{url('login')}}">
                                            <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                                        </a>
                                    </p>

                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-primary btn-prev" type="button">
                                            <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                        </button>
                                        <button class="btn btn-primary btn-next" type="button">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                            <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>
                                    </div>
                                </div>


                                <div id="billing" class="content get_form_data" role="tabpanel" aria-labelledby="billing-trigger">
                                    <div class="content-header mb-2">
                                        <h2 class="fw-bolder mb-75">{{ __('locale.labels.select_plan') }}</h2>
                                        <span>{{ __('locale.plans.select_plan_as_per_requirement') }}</span>
                                    </div>

                                    <!-- select plan options -->
                                    <div class="row custom-options-checkable gx-3 gy-2">

                                        @foreach($plans as $plan)
                                            <div class="col-md-4">
                                                <input class="custom-option-item-check" type="radio" name="plans" id="{{ $plan->id }}" value="{{ $plan->id }}"/>
                                                <label class="custom-option-item text-center p-1" for="{{ $plan->id }}">
                                                    <span class="custom-option-item-title h3 fw-bolder">{{ $plan->name }}</span>
                                                    <span class="d-block m-75">{{ $plan->description }}</span>
                                                    <span class="plan-price">
                                                    <span class="pricing-value fw-bolder text-primary">{{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}</span>
                                                    <sub class="pricing-duration text-body font-medium-1 fw-bold">/{{ $plan->displayFrequencyTime() }}</sub>
                                                </span>
                                                </label>
                                            </div>
                                        @endforeach


                                    </div>
                                    <!-- / select plan options -->

                                    <div class="content-header my-2 py-1">
                                        <h2 class="fw-bolder mb-75">{{ __('locale.labels.payment_options') }}</h2>
                                        <span>{{ __('locale.payment_gateways.click_on_correct_option') }}</span>
                                    </div>

                                    <div class="row gx-2">
                                        <ul class="other-payment-options list-unstyled">
                                            @foreach($payment_methods as $method)
                                                <li>
                                                    <div class="form-check mt-1">
                                                        <input type="radio" name="payment_methods" class="form-check-input" value="{{$method->type}}">
                                                        <label class="form-check-label">{{ $method->name }}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <p class="mt-1 mb-1">
                                        <a href="{{url('login')}}">
                                            <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                                        </a>
                                    </p>

                                    <div class="d-flex justify-content-between mt-1">
                                        <button class="btn btn-primary btn-prev" type="button">
                                            <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                        </button>
                                        <button class="btn btn-success btn-submit" type="submit">
                                            <i data-feather="check" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Submit</span>
                                        </button>
                                    </div>

                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset(mix('vendors/js/forms/wizard/bs-stepper.min.js'))}}"></script>
    <script src="{{asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>
@endsection

@section('page-script')

    <script>
        let registerMultiStepsWizard = document.querySelector('.register-multi-steps-wizard'),
            pageResetForm = $('.auth-register-form'),
            numberedStepper,
            select = $('.select2');


        // multi-steps registration
        // --------------------------------------------------------------------

        // Horizontal Wizard
        if (typeof registerMultiStepsWizard !== undefined && registerMultiStepsWizard !== null) {
            numberedStepper = new Stepper(registerMultiStepsWizard);

            $(registerMultiStepsWizard)
                .find('.btn-next')
                .each(function () {
                    $(this).on('click', function (e) {

                        let email = $('#email').val(),
                            password = $('#password').val(),
                            confirm_password = $('#password_confirmation').val();

                        if (email != null && password != null && confirm_password != null && password === confirm_password) {
                            numberedStepper.next();
                        } else {
                            e.preventDefault();

                            toastr['error']("{{ __('locale.auth.insert_required_fields') }}", 'Oops..!!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    });
                });

            $(registerMultiStepsWizard)
                .find('.btn-prev')
                .on('click', function () {
                    numberedStepper.previous();
                });
        }

        // select2
        select.each(function () {
            let $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $this.parent()
            });
        });

    </script>
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
