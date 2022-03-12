@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.purchase'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
@endsection

@section('content')


    <!-- Modern Horizontal Wizard -->
    <section class="modern-horizontal-wizard">
        <form action="{{route('customer.subscriptions.purchase', $plan->uid)}}" method="post">
            @csrf

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            {{ $error }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif

            <div class="bs-stepper wizard-modern modern-wizard-example">
                <div class="bs-stepper-header">

                    <div class="step" data-target="#cart" role="tab" id="cart-trigger">
                        <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">
                            <i data-feather="shopping-cart" class="font-medium-3"></i>
                        </span>
                            <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('locale.labels.cart') }}</span>
                            <span class="bs-stepper-subtitle">{{ $plan->name }} {{ __('locale.menu.Plan') }}</span>
                        </span>
                        </button>
                    </div>
                    <div class="line">
                        <i data-feather="chevron-right" class="font-medium-2"></i>
                    </div>


                    <div class="step" data-target="#address" role="tab" id="address-trigger">
                        <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">
                            <i data-feather="map-pin" class="font-medium-3"></i>
                        </span>
                            <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('locale.labels.address') }}</span>
                            <span class="bs-stepper-subtitle">{{ __('locale.labels.billing_address') }}</span>
                        </span>
                        </button>
                    </div>
                    <div class="line">
                        <i data-feather="chevron-right" class="font-medium-2"></i>
                    </div>


                    <div class="step" data-target="#payment" role="tab" id="payment-trigger">
                        <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">
                            <i data-feather="credit-card" class="font-medium-3"></i>
                        </span>
                            <span class="bs-stepper-label">
                            <span class="bs-stepper-title">{{ __('locale.labels.payment') }}</span>
                            <span class="bs-stepper-subtitle">{{ __('locale.labels.pay_payment') }}</span>
                        </span>
                        </button>
                    </div>
                </div>


                <div class="bs-stepper-content">

                    <div id="cart" class="content" role="tabpanel" aria-labelledby="cart-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">{{ __('locale.menu.Subscriptions') }}</h5>
                            <small class="text-muted">{!!  __('locale.subscription.log_subscribe', ['plan' => $plan->name]) !!}</small>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td> {{ __('locale.plans.price') }} </td>
                                        <td> {{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }} </td>
                                    </tr>

                                    <tr>
                                        <td> {{ __('locale.labels.renew') }} </td>
                                        <td> {{ __('locale.labels.every') }} {{ $plan->displayFrequencyTime() }} </td>
                                    </tr>

                                    <tr>
                                        <td> {{ __('locale.labels.sms_credit') }} </td>
                                        <td> {{ $plan->displayTotalQuota() }} </td>
                                    </tr>

                                    <tr>
                                        <td> {{ __('locale.plans.create_own_sending_server') }} </td>
                                        <td>
                                            @if($plan->getOption('create_sending_server') == 'yes')
                                                {{__('locale.labels.yes')}}
                                            @else
                                                {{__('locale.labels.no')}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td> {{ __('locale.customer.sender_id_verification') }} </td>
                                        <td>
                                            @if($plan->getOption('sender_id_verification') == 'yes')
                                                {{__('locale.labels.yes')}}
                                            @else
                                                {{__('locale.labels.no')}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td> {{ __('locale.labels.cutting_system_available') }} </td>
                                        <td>
                                            {{__('locale.labels.yes')}}
                                        </td>
                                    </tr>


                                    <tr>
                                        <td> {{ __('locale.labels.api_access') }} </td>
                                        <td>
                                            @if($plan->getOption('api_access') == 'yes')
                                                {{__('locale.labels.yes')}}
                                            @else
                                                {{__('locale.labels.no')}}
                                            @endif
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button class="btn btn-outline-secondary btn-prev" disabled>
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                            </button>
                            <button class="btn btn-primary btn-next" type="button">
                                <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                            </button>
                        </div>
                    </div>

                    <div id="address" class="content" role="tabpanel" aria-labelledby="address-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">{{ __('locale.labels.address') }}</h5>
                            <small>{{ __('locale.labels.billing_address') }}</small>
                        </div>
                        <div class="row">

                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="first_name" class="required form-label">{{ __('locale.labels.first_name') }}</label>
                                    <input type="text" id="first_name" class="form-control required" name="first_name" value="{{Auth::user()->first_name}}">
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="last_name">{{ __('locale.labels.last_name') }}</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" value="{{Auth::user()->last_name}}">
                                </div>
                            </div>


                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="email" class="required form-label">{{ __('locale.labels.email') }}</label>
                                    <input type="email" id="email" class="form-control required" name="email" value="{{Auth::user()->email}}">
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="phone" class="required form-label">{{ __('locale.labels.phone') }}</label>
                                    <input type="number" id="phone" class="form-control required" name="phone" value="{{ Auth::user()->customer->phone }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="address" class="required form-label">{{ __('locale.labels.address') }}</label>
                                    <input type="text" id="address" class="form-control required" name="address" value="{{ Auth::user()->customer->financial_address }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="city" class="required form-label">{{ __('locale.labels.city') }}</label>
                                    <input type="text" id="city" class="form-control required" name="city" value="{{ Auth::user()->customer->financial_city }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="postcode" class="form-label">{{ __('locale.labels.postcode') }}</label>
                                    <input type="text" id="postcode" class="form-control" name="postcode" value="{{ Auth::user()->customer->financial_postcode }}">
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="mb-1">
                                    <label for="country" class="required form-label">{{__('locale.labels.country')}}</label>
                                    <select class="form-select select2" id="country" name="country">
                                        @foreach(\App\Helpers\Helper::countries() as $country)
                                            <option value="{{$country['name']}}" {{ Auth::user()->customer->country == $country['name'] ? 'selected': null }}> {{ $country['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary btn-prev" type="button">
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                            </button>
                            <button class="btn btn-primary btn-next" type="button">
                                <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                            </button>
                        </div>
                    </div>

                    <div id="payment" class="content" role="tabpanel" aria-labelledby="payment-trigger">
                        <div class="content-header">
                            <h5 class="mb-0">{{ __('locale.labels.payment_options') }}</h5>
                            <small>{{ __('locale.payment_gateways.click_on_correct_option') }}</small>
                        </div>
                        <div class="row mb-2 mt-2 ">
                            <ul class="other-payment-options list-unstyled">

                                @foreach($payment_methods as $method)
                                    <li class="py-50">
                                        <div class="form-check">
                                            <input type="radio" id="{{$method->type}}" value="{{$method->type}}" name="payment_methods" class="form-check-input"/>
                                            <label class="form-check-label" for="{{$method->type}}"> {{ $method->name }} </label>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary btn-prev" type="button">
                                <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                            </button>
                            <button class="btn btn-success btn-submit" type="submit">{{ __('locale.labels.checkout') }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </section>
    <!-- /Modern Horizontal Wizard -->

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/wizard/bs-stepper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-wizard.js')) }}"></script>
@endsection
