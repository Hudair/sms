@extends('layouts/contentLayoutMaster')

@section('title', $plan->name)

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/noui-slider.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-noui.css')) }}">
@endsection

@section('content')


    <section id="nav-justified">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h4 class="card-title">{{ $plan->name }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <code>  {{ __('locale.description.plan_details') }} </code>
                            <ul class="nav nav-tabs nav-justified mt-5" id="myTab2" role="tablist">

                                {{-- Gerenal --}}
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab-justified" data-toggle="tab" href="#general-just" role="tab"
                                       aria-controls="general-just" aria-selected="true">{{ __('locale.labels.general') }}</a>
                                </li>

                                {{-- features setting --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="features-tab-justified" data-toggle="tab" href="#features-just" role="tab"
                                       aria-controls="features-just" aria-selected="true">{{ __('locale.plans.plan_features') }}</a>
                                </li>

                                {{-- speed limit --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="speed-limit-tab-justified" data-toggle="tab" href="#speed-limit-just" role="tab"
                                       aria-controls="speed-limit-just" aria-selected="true">{{ __('locale.plans.speed_limit') }}</a>
                                </li>

                                {{-- cutting system --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="cutting-system-tab-justified" data-toggle="tab" href="#cutting-system-just" role="tab"
                                       aria-controls="cutting-system-just" aria-selected="true">{{ __('locale.sending_servers.cutting_system') }}</a>
                                </li>

                                {{-- Sending Server --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="sending-server-tab-justified" data-toggle="tab" href="#sending-server-just" role="tab"
                                       aria-controls="sending-server-just" aria-selected="false">
                                        @if (!$plan->hasPrimarySendingServer())
                                            <i class="feather icon-alert-circle text-danger"></i>
                                        @endif
                                        {{ __('locale.menu.Sending Servers') }}</a>
                                </li>

                                {{-- Coverage --}}
                                <li class="nav-item">
                                    <a class="nav-link" id="pricing-tab-justified" data-toggle="tab" href="#pricing-just" role="tab"
                                       aria-controls="pricing-just" aria-selected="false">{{ __('locale.plans.pricing') }}</a>
                                </li>
                            </ul>


                            {{-- Tab panes --}}
                            <div class="tab-content pt-1">


                                {{-- Gerenal --}}
                                <div class="tab-pane active" id="general-just" role="tabpanel" aria-labelledby="general-tab-justified">
                                    @include('admin.plans._general')
                                </div>


                                {{-- features setting --}}
                                <div class="tab-pane" id="features-just" role="tabpanel" aria-labelledby="features-tab-justified">
                                    @include('admin.plans._features')
                                </div>

                                {{-- speed limit --}}
                                <div class="tab-pane" id="speed-limit-just" role="tabpanel" aria-labelledby="speed-limit-tab-justified">
                                    @include('admin.plans._speed_limit')
                                </div>

                                {{-- cutting system --}}
                                <div class="tab-pane" id="cutting-system-just" role="tabpanel" aria-labelledby="cutting-system-tab-justified">
                                    @include('admin.plans._cutting_system')
                                </div>


                                {{-- Sending Server --}}
                                <div class="tab-pane" id="sending-server-just" role="tabpanel" aria-labelledby="sending-server-tab-justified">
                                    @include('admin.plans._sending_server')
                                </div>


                                {{-- pricing --}}
                                <div class="tab-pane" id="pricing-just" role="tabpanel" aria-labelledby="pricing-tab-justified">
                                    @include('admin.plans._pricing')
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/wNumb.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/nouislider.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection


@section('page-script')
    @if($errors->has('sending_server_id'))
        <script>
            $(function () {
                $('#addSendingSever').modal({
                    show: true
                });
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {

            //show response message
            function showResponseMessage(data) {

                if (data.status === 'success') {
                    toastr.success(data.message, 'Success!!', {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.warning("{{__('locale.exceptions.something_went_wrong')}}", "{{__('locale.labels.attention')}}", {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                }
            }

            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
            });

            let showCustom = $('.show-custom');
            let showCustomSendingLimit = $('.show-custom-sending-limit');
            let billing_cycle = $('#billing_cycle');
            let sending_limit = $('#sending_limit');

            // init table dom
            let Table = $("table");

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            if (billing_cycle.val() === 'custom') {
                showCustom.show();
            } else {
                showCustom.hide();
            }

            billing_cycle.on('change', function () {
                if (billing_cycle.val() === 'custom') {
                    showCustom.show();
                } else {
                    showCustom.hide();
                }

            });

            if (sending_limit.val() === 'custom') {
                showCustomSendingLimit.show();
            } else {
                showCustomSendingLimit.hide();
            }

            sending_limit.on('change', function () {
                if (sending_limit.val() === 'custom') {
                    showCustomSendingLimit.show();
                } else {
                    showCustomSendingLimit.hide();
                }
            });


            $('#sms_max').on('click', function () {
                $('.sms-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            $('#whatsapp_max').on('click', function () {
                $('.whatsapp-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            $('#list_max').on('click', function () {
                $('.list-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            $('#subscriber_max').on('click', function () {
                $('.subscriber-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            $('#subscriber_per_list_max').on('click', function () {
                $('.subscriber-per-list-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            $('#segment_per_list_max').on('click', function () {
                $('.segment-per-list-max-input').prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });


            //set primary
            Table.delegate(".action-set-primary", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.plans.sending_server_as_primary') }}",
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.set_primary') }}",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('admin.plans.settings.set-primary', $plan->uid)}}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                server_id: id,
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                showResponseMessage(data);
                            },
                            error: function (reject) {
                                if (reject.status === 422) {
                                    let errors = reject.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                            positionClass: 'toast-top-right',
                                            containerId: 'toast-top-right',
                                            progressBar: true,
                                            closeButton: true,
                                            newestOnTop: true
                                        });
                                    });
                                } else {
                                    toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                }
                            }
                        })
                    }
                })
            });


            //delete sending server
            Table.delegate(".action-delete", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    type: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('admin.plans.settings.delete-sending-server', $plan->uid)}}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                server_id: id,
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                showResponseMessage(data);
                            },
                            error: function (reject) {
                                if (reject.status === 422) {
                                    let errors = reject.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                            positionClass: 'toast-top-right',
                                            containerId: 'toast-top-right',
                                            progressBar: true,
                                            closeButton: true,
                                            newestOnTop: true
                                        });
                                    });
                                } else {
                                    toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                }
                            }
                        })
                    }
                })
            });
        });
    </script>
@endsection
