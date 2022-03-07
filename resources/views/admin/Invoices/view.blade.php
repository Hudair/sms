@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.invoice'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/invoice.css')) }}">
@endsection
@section('content')
    <!-- invoice functionality start -->
    <section class="invoice-print mb-1">
        <div class="row">
            <div class="col-12 col-md-12 d-flex flex-column flex-md-row justify-content-end">
                <button class="btn btn-primary btn-print mb-1 mb-md-0"><i class="feather icon-printer"></i> {{__('locale.labels.print')}}</button>
            </div>
        </div>
    </section>
    <!-- invoice functionality end -->
    <section class="card invoice-page">
        <div id="invoice-template" class="card-body">
            <!-- Invoice Company Details -->
            <div id="invoice-company-details" class="row">
                <div class="col-md-6 col-sm-12 text-left pt-1">
                    <div class="media pt-1">
                        <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}" class=""/>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <h1>{{__('locale.labels.invoice')}}</h1>
                    <div class="invoice-details mt-2">
                        <h6 class="text-uppercase">{{__('locale.labels.invoice_number')}}.</h6>
                        <p>{{ $invoice->uid }}</p>
                        <h6 class="mt-2 text-uppercase">{{ __('locale.labels.invoice_date') }}</h6>
                        <p>{{ \App\Library\Tool::customerDateTime($invoice->created_at) }}</p>
                    </div>
                </div>
            </div>
            <!--/ Invoice Company Details -->

            <!-- Invoice Recipient Details -->
            <div id="invoice-customer-details" class="row pt-2">
                <div class="col-sm-6 col-12 text-left">
                    <h5>{{ __('locale.labels.recipient') }}</h5>
                    <div class="recipient-info my-2">
                        <p>{{ $invoice->user->displayName() }}</p>
                        <p>{{ $invoice->user->customer->financial_address }}</p>
                        <p>{{ $invoice->user->customer->financial_city }}</p>
                        <p>{{ $invoice->user->customer->financial_postcode }}</p>
                        <p>{{ $invoice->user->customer->state }}</p>
                        <p>{{ $invoice->user->customer->country }}</p>
                    </div>
                    <div class="recipient-contact pb-2">
                        <p><i class="feather icon-mail"></i> {{ $invoice->user->email }}</p>
                        <p><i class="feather icon-phone"></i> {{ $invoice->user->customer->phone }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-12 text-right">
                    <h5>{{ config('app.name') }}</h5>
                    <div class="company-info my-2">
                        {!! \App\Helpers\Helper::app_config('company_address') !!}
                    </div>
                    <div class="company-contact">
                        <p><i class="feather icon-mail"></i> {{ \App\Helpers\Helper::app_config('from_email') }}</p>
                        @if(\App\Helpers\Helper::app_config('notification_phone'))
                            <p><i class="feather icon-phone"></i> {{ \App\Helpers\Helper::app_config('notification_phone') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!--/ Invoice Recipient Details -->

            <!-- Invoice Items Details -->
            <div id="invoice-items-details" class="pt-1 invoice-items-table">
                <div class="row">
                    <div class="table-responsive col-sm-12">
                        <table class="table table-borderless">
                            <thead>
                            <tr class="text-uppercase">
                                <th>{{ __('locale.labels.payment_details') }}</th>
                                <th>{{ __('locale.labels.status') }}</th>
                                <th>{{ __('locale.labels.type') }}</th>
                                <th>{{ __('locale.labels.amount') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ $invoice->description }}</td>
                                <td>{{ ucfirst($invoice->status) }}</td>
                                <td>{{ ucfirst($invoice->type) }}</td>
                                <td>{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="invoice-total-details" class="invoice-total-table">
                <div class="row">
                    <div class="col-7 offset-5">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                <tr>
                                    <th class="text-uppercase">{{ __('locale.labels.subtotal') }}</th>
                                    <td>{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase">{{ __('locale.labels.delivery_charge') }}</th>
                                    <td class="text-success">{{ __('locale.labels.free') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase">{{ __('locale.labels.total') }}</th>
                                    <td>{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Footer -->
            <div id="invoice-footer" class="text-right pt-3">
                <p>{{ __('locale.subscription.invoice_successfully_paid') }}. {{ __('locale.labels.payment_information') }}
                <p class="bank-details mb-0">
                    <span class="mr-4">{{ __('locale.labels.paid_by') }}: <strong>{{ $invoice->paymentMethod->name }}</strong></span>
                    <span>{{ __('locale.labels.transaction_id') }}: <strong>{{ $invoice->transaction_id }}</strong></span>
                </p>
            </div>
            <!--/ Invoice Footer -->
        </div>
    </section>
@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            // print invoice with button
            $(".btn-print").click(function () {
                window.print();
            });
        });
    </script>
@endsection
