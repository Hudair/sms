@extends('layouts/contentLayoutMaster')

@section('title', $gateway->name)

@section('content')

    {{-- Vertical Tabs start --}}
    <section id="vertical-tabs">

        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">{{ $gateway->name }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            @switch($gateway->type)
                                @case('paypal')
                                <p>{!!  __('locale.description.paypal') !!}</p>
                                @break

                                @case('braintree')
                                <p>{!!  __('locale.description.braintree') !!}</p>
                                @break

                                @case('stripe')
                                <p>{!!  __('locale.description.stripe') !!}</p>
                                @break

                                @case('authorize_net')
                                <p>{!!  __('locale.description.authorize_net') !!}</p>
                                @break

                                @case('2checkout')
                                <p>{!!  __('locale.description.2checkout') !!}</p>
                                @break

                                @case('paystack')
                                <p>{!!  __('locale.description.paystack', ['callback_url' => route('customer.callback.paystack')]) !!}</p>
                                @break

                                @case('paynow')
                                <p>{!!  __('locale.description.paynow') !!}</p>
                                @break

                                @case('razorpay')
                                <p>{!!  __('locale.description.razorpay',[
                                       'callback_url_senderid' => route('customer.callback.razorpay.senderid'),
                                       'callback_url_keywords' => route('customer.callback.razorpay.keywords'),
                                       'callback_url_subscriptions' => route('customer.callback.razorpay.subscriptions'),
                                        ])!!}</p>
                                @break


                            @endswitch

                            <form class="form form-vertical" action="{{ route('admin.payment-gateways.update', $gateway->uid) }}" method="post">
                                @method('PUT')
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="secret" name="name" class="form-control" value="{{ $gateway->name }}" required>
                                                <span class="text-muted">{{__('locale.payment_gateways.rename_name')}}</span>
                                                @error('name')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if($gateway->type == \App\Models\PaymentMethods::TYPE_PAYPAL)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="client_id" class="required">{{ __('locale.labels.client_id') }}</label>
                                                    <input type="text" id="client_id" name="client_id" class="form-control" value="{{ $gateway->getOption('client_id') }}" required>
                                                    @error('client_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="secret" class="required">{{ __('locale.labels.client_secret') }}</label>
                                                    <input type="text" id="secret" name="secret" class="form-control" value="{{ $gateway->getOption('secret') }}" required>
                                                    @error('secret')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_BRAINTREE)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_id" class="required">{{ __('locale.labels.merchant_id') }}</label>
                                                    <input type="text" id="merchant_id" name="merchant_id" class="form-control" value="{{ $gateway->getOption('merchant_id') }}" required>
                                                    @error('merchant_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="public_key" class="required">{{ __('locale.labels.public_key') }}</label>
                                                    <input type="text" id="public_key" name="public_key" class="form-control" value="{{ $gateway->getOption('public_key') }}" required>
                                                    @error('public_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="private_key" class="required">{{ __('locale.labels.private_key') }}</label>
                                                    <input type="text" id="private_key" name="private_key" class="form-control" value="{{ $gateway->getOption('private_key') }}" required>
                                                    @error('private_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_STRIPE)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="publishable_key" class="required">{{ __('locale.labels.publishable_key') }}</label>
                                                    <input type="text" id="publishable_key" name="publishable_key" class="form-control" value="{{ $gateway->getOption('publishable_key') }}" required>
                                                    @error('publishable_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="secret_key" class="required">{{ __('locale.labels.secret_key') }}</label>
                                                    <input type="text" id="secret_key" name="secret_key" class="form-control" value="{{ $gateway->getOption('secret_key') }}" required>
                                                    @error('secret_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_AUTHORIZE_NET)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="login_id" class="required">{{ __('locale.labels.login_id') }}</label>
                                                    <input type="text" id="login_id" name="login_id" class="form-control" value="{{ $gateway->getOption('login_id') }}" required>
                                                    @error('login_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="transaction_key" class="required">{{ __('locale.labels.transaction_key') }}</label>
                                                    <input type="text" id="transaction_key" name="transaction_key" class="form-control" value="{{ $gateway->getOption('transaction_key') }}" required>
                                                    @error('transaction_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_2CHECKOUT)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_code" class="required">{{ __('locale.labels.merchant_code') }}</label>
                                                    <input type="text" id="merchant_code" name="merchant_code" class="form-control" value="{{ $gateway->getOption('merchant_code') }}" required>
                                                    @error('merchant_code')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="private_key" class="required">{{ __('locale.labels.private_key') }}</label>
                                                    <input type="text" id="private_key" name="private_key" class="form-control" value="{{ $gateway->getOption('private_key') }}" required>
                                                    @error('private_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_PAYSTACK)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="public_key" class="required">{{ __('locale.labels.public_key') }}</label>
                                                    <input type="text" id="public_key" name="public_key" class="form-control" value="{{ $gateway->getOption('public_key') }}" required>
                                                    @error('public_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="secret_key" class="required">{{ __('locale.labels.secret_key') }}</label>
                                                    <input type="text" id="secret_key" name="secret_key" class="form-control" value="{{ $gateway->getOption('secret_key') }}" required>
                                                    @error('secret_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_email" class="required">{{ __('locale.labels.merchant_email') }}</label>
                                                    <input type="email" id="merchant_email" name="merchant_email" class="form-control" value="{{ $gateway->getOption('merchant_email') }}" required>
                                                    @error('merchant_email')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_PAYU)
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="client_id" class="required">{{ __('locale.labels.client_id') }}</label>
                                                    <input type="text" id="client_id" name="client_id" class="form-control" value="{{ $gateway->getOption('client_id') }}" required>
                                                    @error('client_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="client_secret" class="required">{{ __('locale.labels.client_secret') }}</label>
                                                    <input type="text" id="secret" name="client_secret" class="form-control" value="{{ $gateway->getOption('client_secret') }}" required>
                                                    @error('client_secret')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_SLYDEPAY)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_email" class="required">{{ __('locale.labels.merchant_email') }}</label>
                                                    <input type="email" id="merchant_email" name="merchant_email" class="form-control" value="{{ $gateway->getOption('merchant_email') }}" required>
                                                    @error('merchant_email')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_secret" class="required">{{ __('locale.labels.merchant_secret') }}</label>
                                                    <input type="text" id="secret" name="merchant_secret" class="form-control" value="{{ $gateway->getOption('merchant_secret') }}" required>
                                                    @error('merchant_secret')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_PAYNOW)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="integration_id" class="required">{{ __('locale.labels.integration_id') }}</label>
                                                    <input type="text" id="integration_id" name="integration_id" class="form-control" value="{{ $gateway->getOption('integration_id') }}" required>
                                                    @error('integration_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="integration_key" class="required">{{ __('locale.labels.integration_key') }}</label>
                                                    <input type="text" id="secret" name="integration_key" class="form-control" value="{{ $gateway->getOption('integration_key') }}" required>
                                                    @error('integration_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_COINPAYMENTS)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_id" class="required">{{ __('locale.labels.merchant_id') }}</label>
                                                    <input type="text" id="secret" name="merchant_id" class="form-control" value="{{ $gateway->getOption('merchant_id') }}" required>
                                                    @error('merchant_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_INSTAMOJO)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="api_key" class="required">{{ __('locale.labels.api_key') }}</label>
                                                    <input type="text" id="secret" name="api_key" class="form-control" value="{{ $gateway->getOption('api_key') }}" required>
                                                    @error('api_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="auth_token" class="required">{{ __('locale.labels.auth_token') }}</label>
                                                    <input type="text" id="secret" name="auth_token" class="form-control" value="{{ $gateway->getOption('auth_token') }}" required>
                                                    @error('auth_token')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_PAYUMONEY)


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_key" class="required">{{ __('locale.labels.merchant_key') }}</label>
                                                    <input type="text" id="secret" name="merchant_key" class="form-control" value="{{ $gateway->getOption('merchant_key') }}" required>
                                                    @error('merchant_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="merchant_salt" class="required">{{ __('locale.labels.merchant_salt') }}</label>
                                                    <input type="text" id="secret" name="merchant_salt" class="form-control" value="{{ $gateway->getOption('merchant_salt') }}" required>
                                                    @error('merchant_salt')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_RAZORPAY)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="key_id" class="required">{{ __('locale.labels.key_id') }}</label>
                                                    <input type="text" id="secret" name="key_id" class="form-control" value="{{ $gateway->getOption('key_id') }}" required>
                                                    @error('key_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="key_secret" class="required">{{ __('locale.labels.key_secret') }}</label>
                                                    <input type="text" id="secret" name="key_secret" class="form-control" value="{{ $gateway->getOption('key_secret') }}" required>
                                                    @error('key_secret')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_SSLCOMMERZ)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="store_id" class="required">{{ __('locale.labels.store_id') }}</label>
                                                    <input type="text" id="secret" name="store_id" class="form-control" value="{{ $gateway->getOption('store_id') }}" required>
                                                    @error('store_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="store_passwd" class="required">{{ __('locale.labels.store_password') }}</label>
                                                    <input type="text" id="secret" name="store_passwd" class="form-control" value="{{ $gateway->getOption('store_passwd') }}" required>
                                                    @error('store_passwd')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_AAMARPAY)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="store_id" class="required">{{ __('locale.labels.store_id') }}</label>
                                                    <input type="text" id="secret" name="store_id" class="form-control" value="{{ $gateway->getOption('store_id') }}" required>
                                                    @error('store_id')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="signature_key" class="required">{{ __('locale.labels.signature_key') }}</label>
                                                    <input type="text" id="signature_key" name="signature_key" class="form-control" value="{{ $gateway->getOption('signature_key') }}" required>
                                                    @error('signature_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_FLUTTERWAVE)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="environment" class="required">{{ __('locale.labels.environment') }}</label>
                                                    <select class="form-control" name="environment" id="environment">
                                                        <option value="sandbox" @if($gateway->getOption('environment') == 'sandbox' ) selected @endif>{{ __('locale.labels.sandbox') }}</option>
                                                        <option value="production" @if($gateway->getOption('environment') == 'production' ) selected @endif>{{ __('locale.labels.production')}} </option>
                                                    </select>
                                                    @error('environment')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="public_key" class="required">{{ __('locale.labels.public_key') }}</label>
                                                    <input type="text" id="secret" name="public_key" class="form-control" value="{{ $gateway->getOption('public_key') }}" required>
                                                    @error('public_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="secret_key" class="required">{{ __('locale.labels.secret_key') }}</label>
                                                    <input type="text" id="secret_key" name="secret_key" class="form-control" value="{{ $gateway->getOption('secret_key') }}" required>
                                                    @error('secret_key')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @elseif($gateway->type == \App\Models\PaymentMethods::TYPE_CASH)

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="payment_details" class="required">{{ __('locale.labels.payment_details') }}</label>
                                                    <textarea rows="7" class="form-control" name="payment_details" required>{!! $gateway->getOption('payment_details') !!}</textarea>
                                                    @error('payment_details')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="payment_confirmation" class="required">{{ __('locale.labels.payment_confirmation') }}</label>
                                                    <textarea rows="7" class="form-control" name="payment_confirmation" required>{!! $gateway->getOption('payment_confirmation') !!}</textarea>
                                                    @error('payment_confirmation')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <p class="text-danger text-bold-600">{{ __('locale.payment_gateways.not_found') }}</p>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <input type="hidden" value="{{$gateway->type}}" name="type">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Vertical Tabs end --}}
@endsection

