@extends('layouts/contentLayoutMaster')

@section('title', $plan->name)

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection


@section('content')
    <section id="nav-justified">
        <div class="row">
            <div class="col-12">

                <code>  {{ __('locale.description.plan_details') }} </code>
                <ul class="nav nav-pills mb-2 mt-2 text-uppercase" role="tablist">

                    {{-- Gerenal --}}
                    <li class="nav-item">
                        <a class="nav-link  @if (old('tab') == 'general' || old('tab') == null) active @endif" id="general-tab-justified" data-bs-toggle="tab" href="#general" role="tab"
                           aria-controls="general" aria-selected="true"><i data-feather="home"></i> {{ __('locale.labels.general') }}</a>
                    </li>

                    {{-- features setting --}}
                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'features' ? 'active':null }}" id="features-tab-justified" data-bs-toggle="tab" href="#features" role="tab"
                           aria-controls="features" aria-selected="true"><i data-feather="package"></i> {{ __('locale.plans.plan_features') }}</a>
                    </li>

                    {{-- speed limit --}}
                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'speed_limit' ? 'active':null }}" id="speed-limit-tab-justified" data-bs-toggle="tab" href="#speed-limit" role="tab"
                           aria-controls="speed-limit" aria-selected="true"><i data-feather="send"></i> {{ __('locale.plans.speed_limit') }}</a>
                    </li>

                    {{-- cutting system --}}
                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'cutting_system' ? 'active':null }}" id="cutting-system-tab-justified" data-bs-toggle="tab" href="#cutting-system" role="tab"
                           aria-controls="cutting-system" aria-selected="true"><i data-feather="minus-square"></i> {{ __('locale.sending_servers.cutting_system') }}</a>
                    </li>

                    {{-- Sending Server --}}
                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'sending_server' ? 'active':null }}" id="sending-server-tab-justified" data-bs-toggle="tab" href="#sending-server" role="tab"
                           aria-controls="sending-server" aria-selected="false">
                            @if (!$plan->hasPrimarySendingServer())
                                <i data-feather="alert-circle" class="text-danger"></i>
                            @else
                                <i data-feather="server"></i>
                            @endif
                            {{ __('locale.menu.Sending Servers') }}</a>
                    </li>

                    {{-- Coverage --}}
                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'pricing' ? 'active':null }}" id="pricing-tab-justified" data-bs-toggle="tab" href="#pricing" role="tab"
                           aria-controls="pricing" aria-selected="false"><i data-feather="shopping-cart"></i> {{ __('locale.plans.pricing') }}</a>
                    </li>
                </ul>


                {{-- Tab panes --}}
                <div class="tab-content pt-1">

                    {{-- Gerenal --}}
                    <div class="tab-pane @if (old('tab') == 'general' || old('tab') == null) active @endif" id="general" role="tabpanel" aria-labelledby="general-tab-justified">
                        @include('admin.plans._general')
                    </div>


                    {{-- features setting --}}
                    <div class="tab-pane {{ old('tab') == 'features' ? 'active':null }}" id="features" role="tabpanel" aria-labelledby="features-tab-justified">
                        @include('admin.plans._features')
                    </div>

                    {{-- speed limit --}}
                    <div class="tab-pane {{ old('tab') == 'speed_limit' ? 'active':null }}" id="speed-limit" role="tabpanel" aria-labelledby="speed-limit-tab-justified">
                        @include('admin.plans._speed_limit')
                    </div>

                    {{-- cutting system --}}
                    <div class="tab-pane {{ old('tab') == 'cutting_system' ? 'active':null }}" id="cutting-system" role="tabpanel" aria-labelledby="cutting-system-tab-justified">
                        @include('admin.plans._cutting_system')
                    </div>


                    {{-- Sending Server --}}
                    <div class="tab-pane {{ old('tab') == 'sending_server' ? 'active':null }}" id="sending-server" role="tabpanel" aria-labelledby="sending-server-tab-justified">
                        @include('admin.plans._sending_server')
                    </div>


                    {{-- pricing --}}
                    <div class="tab-pane {{ old('tab') == 'pricing' ? 'active':null }}" id="pricing" role="tabpanel" aria-labelledby="pricing-tab-justified">
                        @include('admin.plans._pricing')
                    </div>


                </div>
            </div>
        </div>
    </section>

@endsection

@section('vendor-script')
    <!-- vendor files -->

    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
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

            $('#pricing-tab-justified').on('click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            });

            //show response message
            function showResponseMessage(data) {
                if (data.status === 'success') {
                    toastr['success'](data.message, '{{__('locale.labels.success')}}!!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                        rtl: isRtl
                    });
                } else {
                    toastr['warning']("{{__('locale.exceptions.something_went_wrong')}}", '{{ __('locale.labels.warning') }}!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                        rtl: isRtl
                    });
                }
            }


            // Basic Select2 select
            $(".select2").each(function () {
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

            //delete sending server
            Table.delegate(".action-delete", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
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
                                        toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                            closeButton: true,
                                            positionClass: 'toast-top-right',
                                            progressBar: true,
                                            newestOnTop: true,
                                            rtl: isRtl
                                        });
                                    });
                                } else {
                                    toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
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


            // init list view datatable
            let dataListView = $('.datatables-basic').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('admin.plans.settings.search_coverage', $plan->uid) }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": 'responsive_id', orderable: false, searchable: false},
                    {"data": "uid"},
                    {"data": "name", orderable: false},
                    {"data": "iso_code", orderable: false},
                    {"data": "country_code", orderable: false},
                    {"data": "status", orderable: false, searchable: false},
                    {"data": "action", orderable: false, searchable: false}
                ],

                searchDelay: 1500,
                columnDefs: [
                    {
                        // For Responsive
                        className: 'control',
                        orderable: false,
                        responsivePriority: 2,
                        targets: 0
                    },
                    {
                        targets: 1,
                        visible: false
                    },
                    {
                        // Actions
                        targets: -1,
                        title: '{{ __('locale.labels.actions') }}',
                        orderable: false,
                        render: function (data, type, full) {
                            return (

                                '<a href="' + full['edit'] + '" class="text-primary pe-1">' +
                                feather.icons['edit'].toSvg({class: 'font-medium-4'}) +
                                '</a>' +
                                '<span class="action-price-delete text-danger cursor-pointer" data-id=' + full['uid'] + '>' +
                                feather.icons['trash'].toSvg({class: 'font-medium-4'}) +
                                '</span>'

                            );
                        }
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

                language: {
                    paginate: {
                        // remove previous & next text from pagination
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    },
                    sLengthMenu: "_MENU_",
                    sZeroRecords: "{{ __('locale.datatables.no_results') }}",
                    sSearch: "{{ __('locale.datatables.search') }}",
                    sProcessing: "{{ __('locale.datatables.processing') }}",
                    sInfo: "{{ __('locale.datatables.showing_entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}"
                },
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                let data = row.data();
                                return 'Details of ' + data['name'];
                            }
                        }),
                        type: 'column',
                        renderer: function (api, rowIdx, columns) {
                            let data = $.map(columns, function (col) {
                                return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ? '<tr data-dt-row="' +
                                    col.rowIdx +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>'
                                    : '';
                            }).join('');

                            return data ? $('<table class="table"/>').append('<tbody>' + data + '</tbody>') : false;
                        }
                    }
                },
                aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                select: {
                    style: "multi"
                },
                order: [[1, "asc"]],
                displayLength: 10,
            });

            //change status
            Table.delegate(".get_coverage_status", "click", function () {

                let coverage = $(this).data('id');
                let url = "{{ url(config('app.admin_path').'/plans/'.$plan->uid.'/coverage')}}" + '/' + coverage + '/active';

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (data) {
                        showResponseMessage(data);
                    }
                });
            });


            // On Delete
            Table.delegate(".action-price-delete", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');

                let url = "{{ url(config('app.admin_path').'/plans/'.$plan->uid.'/coverage')}}" + '/' + id + '/delete';
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.labels.able_to_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                dataListView.draw();
                                showResponseMessage(data);
                            },
                            error: function (reject) {
                                if (reject.status === 422) {
                                    let errors = reject.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                            closeButton: true,
                                            positionClass: 'toast-top-right',
                                            progressBar: true,
                                            newestOnTop: true,
                                            rtl: isRtl
                                        });
                                    });
                                } else {
                                    toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
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
