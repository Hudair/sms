@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.pay_payment'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
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
        </div>
    </section>
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
                toastr.warning(err.message, "{{__('locale.labels.attention')}}", {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
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
                            toastr.warning(error.message, "{{__('locale.labels.attention')}}", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        }
                    });
                });
            }).catch((error) => {
                if (error) {
                    toastr.warning(error.message, "{{__('locale.labels.attention')}}", {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                }
            });
        });
    </script>

@endsection
