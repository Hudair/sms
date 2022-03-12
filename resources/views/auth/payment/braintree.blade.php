@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

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
                <div class="width-800 mx-auto card px-2 py-2">
                    <form id="payment-form" action="{{ $post_url }}" method="post">
                        @csrf
                        <div id="dropin-container"></div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('locale.labels.pay_payment') }}</button>
                        <input type="hidden" id="nonce" name="payment_method_nonce"/>
                        <input type="hidden" id="device_data" name="device_data"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- // Basic Vertical form layout section end -->

@endsection

@section('page-script')
    <script src="https://js.braintreegateway.com/web/dropin/1.27.0/js/dropin.js"></script>
    <script src="https://js.braintreegateway.com/web/3.76.0/js/client.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.76.0/js/data-collector.min.js"></script>
    <!-- Page js files -->
    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            const form = document.getElementById('payment-form');

            braintree.client.create({
                authorization: "{{ $token }}",
                container: '#dropin-container'
            }).then(function (clientInstance) {


                return braintree.dataCollector.create({
                    client: clientInstance,
                    paypal: true
                }).then(function (dataCollectorInstance) {
                    // At this point, you should access the dataCollectorInstance.deviceData value and provide it
                    // to your server, e.g. by injecting it into your form as a hidden input.
                    document.getElementById('device_data').value = dataCollectorInstance.deviceData;
                });
            }).catch(function (err) {
                toastr['warning'](err.message, "{{__('locale.labels.attention')}}", {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                });
            });

            braintree.dropin.create({
                authorization: "{{ $token }}",
                container: '#dropin-container'
            }).then((dropinInstance) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    dropinInstance.requestPaymentMethod().then((payload) => {
                        document.getElementById('nonce').value = payload.nonce;
                        form.submit();
                    }).catch((error) => {
                        if (error) {
                            toastr['warning'](error.message, "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    });
                });
            }).catch((error) => {
                if (error) {
                    toastr['warning'](error.message, "{{__('locale.labels.attention')}}", {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                        rtl: isRtl
                    });
                }
            });
        });
    </script>

@endsection
