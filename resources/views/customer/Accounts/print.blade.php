@extends('layouts/fullLayoutMaster')

@section('title', __('locale.labels.invoice'))

@section('page-style')
    <link rel="stylesheet" href="{{asset(mix('css/base/pages/app-invoice-print.css'))}}">
@endsection

@section('content')
    <div class="invoice-print p-3">
        <div class="invoice-header d-flex justify-content-between flex-md-row flex-column pb-2">
            <div>
                <div class="d-flex mb-1">
                    <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}" class=""/>
                </div>
                <p class="card-text">{!! \App\Helpers\Helper::app_config('company_address') !!}</p>
            </div>
            <div class="mt-md-0 mt-2">
                <h4 class="font-weight-bold text-right mb-1">{{ __('locale.labels.invoice') }} #{{$invoice->id}}</h4>
                <div class="invoice-date-wrapper mb-50">
                    <span class="invoice-date-title">{{ __('locale.labels.invoice_date') }}:</span>
                    <span class="font-weight-bold"> {{ \App\Library\Tool::formatDate($invoice->created_at) }}</span>
                </div>
            </div>
        </div>

        <hr class="my-2"/>

        <div class="row pb-2">
            <div class="col-sm-6">
                <h6 class="mb-1">{{ __('locale.labels.recipient') }}:</h6>
                <h6 class="mb-25">{{ $invoice->user->displayName() }}</h6>
                <p class="mb-25">{{ $invoice->user->customer->address }}</p>
                <p class="mb-25">{{ $invoice->user->customer->state }}-{{ $invoice->user->customer->postcode }}</p>
                <p class="mb-25">{{ $invoice->user->customer->city }}, {{ $invoice->user->customer->country }}</p>
                <p class="mb-0">{{ $invoice->user->email }}</p>
            </div>
            <div class="col-sm-6 mt-sm-0 mt-2">
                <h6 class="mb-1">{{ __('locale.labels.payment_details') }}:</h6>
                <table>
                    <tbody>
                    <tr>
                        <td class="pr-1">{{ __('locale.labels.total') }}:</td>
                        <td><strong>{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="pr-1">{{ __('locale.labels.paid_by') }}:</td>
                        <td>{{ $invoice->paymentMethod->name }}</td>
                    </tr>
                    <tr>
                        <td class="pr-1">{{ __('locale.labels.transaction_id') }}:</td>
                        <td>{{ $invoice->transaction_id }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive mt-2">
            <table class="table m-0">
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

        <div class="row invoice-sales-total-wrapper mt-3">
            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                <p class="card-text mb-0"></p>
            </div>
            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                <div class="invoice-total-wrapper">
                    <div class="invoice-total-item">
                        <p class="invoice-total-title">{{ __('locale.labels.subtotal') }}:</p>
                        <p class="invoice-total-amount">{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</p>
                    </div>
                    <hr class="my-50"/>
                    <div class="invoice-total-item">
                        <p class="invoice-total-title">{{ __('locale.labels.total') }}:</p>
                        <p class="invoice-total-amount">{{ \App\Library\Tool::format_price($invoice->amount, $invoice->currency->format) }}</p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{asset('js/scripts/pages/app-invoice-print.js')}}"></script>
@endsection
