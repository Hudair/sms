@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.purchase'))

@section('vendor-style')
    <!-- Vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/pages/checkout.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/toastr.css')) }}">
@endsection
@section('content')
    <form action="{{route('customer.subscriptions.purchase', $plan->uid)}}" class="icons-tab-steps checkout-tab-steps wizard-circle" method="post">
    @csrf


    <!-- Checkout Place order starts -->
        <h6><i class="step-icon step feather icon-shopping-cart"></i>{{ __('locale.labels.cart') }}</h6>
        <fieldset class="checkout-step-1 px-0">
            <section id="place-order" class="list-view product-checkout">
                <div class="checkout-items">
                    <div class="card ecommerce-card">
                        <div class="card-content product-description">
                            <div class="card-body">
                                <div class="item-name">
                                    <p>{{ __('locale.subscription.payment_for_plan')  }}: <span class="text-primary">{{ $plan->name }}</span></p>
                                    <p>{{ __('locale.labels.frequency') }}: <span class="text-primary">{{ $plan->displayFrequencyTime() }}</span></p>
                                    <p class="stock-status-in">{{ __('locale.plans.price') }} {{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="checkout-options">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="price-details">
                                    <p>{{ __('locale.sender_id.price_details') }}</p>
                                </div>
                                <div class="detail">
                                    <div class="detail-title">
                                        {{ __('locale.labels.total_price') }}
                                    </div>
                                    <div class="detail-amt">
                                        {{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}
                                    </div>
                                </div>
                                <div class="detail">
                                    <div class="detail-title">
                                        {{ __('locale.labels.delivery_charge') }}
                                    </div>
                                    <div class="detail-amt discount-amt">
                                        {{ __('locale.labels.free') }}
                                    </div>
                                </div>
                                <hr>
                                <div class="detail">
                                    <div class="detail-title detail-total">{{ __('locale.labels.total') }}</div>
                                    <div class="detail-amt total-amt">{{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}</div>
                                </div>
                                <div class="btn btn-primary btn-block place-order text-uppercase">{{ __('locale.labels.place_order') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </fieldset>
        <!-- Checkout Place order Ends -->


        <!-- Checkout Customer Address Starts -->
        <h6><i class="step-icon step feather icon-home"></i>{{ __('locale.labels.address') }}</h6>
        <fieldset class="checkout-step-2 px-0">
            <section id="checkout-address" class="list-view checkout-address ">
                <div class="card">
                    <div class="card-header flex-column align-items-start">
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="first_name" class="required">{{ __('locale.labels.first_name') }}:</label>
                                        <input type="text" id="first_name" class="form-control required" name="first_name" value="{{Auth::user()->first_name}}">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="last_name">{{ __('locale.labels.last_name') }}:</label>
                                        <input type="text" id="last_name" class="form-control" name="last_name" value="{{Auth::user()->last_name}}">
                                    </div>
                                </div>


                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="email" class="required">{{ __('locale.labels.email') }}:</label>
                                        <input type="email" id="email" class="form-control required" name="email" value="{{Auth::user()->email}}">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="phone" class="required">{{ __('locale.labels.phone') }}:</label>
                                        <input type="number" id="phone" class="form-control required" name="phone" value="{{ Auth::user()->customer->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="address" class="required">{{ __('locale.labels.address') }}:</label>
                                        <input type="text" id="address" class="form-control required" name="address" value="{{ Auth::user()->customer->financial_address }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="city" class="required">{{ __('locale.labels.city') }}:</label>
                                        <input type="text" id="city" class="form-control required" name="city" value="{{ Auth::user()->customer->financial_city }}">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="postcode">{{ __('locale.labels.postcode') }}</label>
                                        <input type="text" id="postcode" class="form-control" name="postcode" value="{{ Auth::user()->customer->financial_postcode }}">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="country" class="required">{{__('locale.labels.country')}}</label>
                                        <select class="form-control select2" id="country" name="country">
                                            @foreach(\App\Helpers\Helper::countries() as $country)
                                                <option value="{{$country['name']}}" {{ Auth::user()->customer->country == $country['name'] ? 'selected': null }}> {{ $country['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 offset-md-6">
                                    <div class="btn btn-primary delivery-address float-right">
                                        {{ __('locale.labels.payment') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </fieldset>

        <!-- Checkout Customer Address Ends -->


        <!-- Checkout Payment Starts -->
        <h6><i class="step-icon step feather icon-credit-card"></i>{{ __('locale.labels.payment') }}</h6>
        <fieldset class="checkout-step-3 px-0">
            <section id="checkout-payment" class="list-view product-checkout">
                <div class="payment-type">
                    <div class="card">
                        <div class="card-header flex-column align-items-start">
                            <h4 class="card-title">{{ __('locale.labels.payment_options') }}</h4>
                            <p class="text-muted mt-25">{{ __('locale.payment_gateways.click_on_correct_option') }}</p>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                @error('payment_methods')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror

                                <ul class="other-payment-options list-unstyled">
                                    @foreach($payment_methods as $method)
                                        <li>
                                            <div class="vs-radio-con vs-radio-primary py-25">
                                                <input type="radio" name="payment_methods" value="{{$method->type}}">
                                                <span class="vs-radio">
                                                <span class="vs-radio--border"></span>
                                                <span class="vs-radio--circle"></span>
                                            </span>
                                                <span>{{ $method->name }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="amount-payable checkout-options">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('locale.sender_id.price_details') }}</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="detail">
                                    <div class="details-title">
                                        {{ __('locale.labels.total_price') }}
                                    </div>
                                    <div class="detail-amt">
                                        <strong>{{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}</strong>
                                    </div>
                                </div>
                                <div class="detail">
                                    <div class="details-title">
                                        {{ __('locale.labels.delivery_charge') }}
                                    </div>
                                    <div class="detail-amt discount-amt">
                                        {{ __('locale.labels.free') }}
                                    </div>
                                </div>
                                <hr>
                                <div class="detail">
                                    <div class="details-title">
                                        {{ __('locale.labels.amount_payable') }}
                                    </div>
                                    <div class="detail-amt total-amt">{{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }}</div>
                                </div>

                                <button class="btn btn-primary btn-block text-uppercase" type="submit">{{ __('locale.labels.checkout') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </fieldset>

        <!-- Checkout Payment Starts -->


    </form>
@endsection

@section('vendor-script')
    <!-- Vendor js files -->
    <script src="{{ asset(mix('vendors/js/extensions/jquery.steps.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection

@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/checkout.js')) }}"></script>
    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }


            // Basic Select2 select
            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
            });
        });
    </script>

@endsection
