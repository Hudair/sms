@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.pay_payment'))

@section('page-style')
    <style>
        .card-body p{
            line-height: 0.8;
        }
    </style>
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
                            {!! $data->payment_details !!}
                            <br>
                            <h6 class="text-uppercase">For {{ __('locale.labels.payment_confirmation') }}:</h6>
                            {!! $data->payment_confirmation !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->

@endsection
