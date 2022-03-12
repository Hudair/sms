@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.billing'))


@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection


@section('content')
    <section id="vertical-tabs">
        <div class="row match-height">
            <div class="col-12">

                <ul class="nav nav-pills mb-2 text-uppercase" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link  @if (old('tab') == 'invoice' || old('tab') == null) active @endif" id="invoice" data-bs-toggle="tab" aria-controls="invoice" href="#invoice" role="tab" aria-selected="true"><i data-feather="shopping-cart"></i> {{ __('locale.labels.invoices') }}</a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link"
                           id="subscriptions-tab"
                           data-bs-toggle="tab"
                           aria-controls="subscriptions"
                           href="#subscriptions" role="tab"
                           aria-selected="false">
                            <i data-feather="credit-card"></i>
                            {{ __('locale.menu.Subscriptions') }}
                        </a>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link {{ old('tab') == 'preferences' ? 'active':null }}" id="preferences-tab" data-bs-toggle="tab" aria-controls="preferences"
                           href="#preferences" role="tab" aria-selected="false"> <i data-feather="settings"></i> {{__('locale.labels.preferences')}} </a>
                    </li>

                </ul>

                <div class="tab-content">

                    <div class="tab-pane  @if (old('tab') == 'invoice' || old('tab') == null) active @endif" id="invoice" role="tabpanel" aria-labelledby="invoice">
                        @include('customer.Accounts._invoices')
                    </div>

                    <div class="tab-pane" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                        @include('customer.Accounts._subscriptions')
                    </div>

                    <div class="tab-pane {{ old('tab') == 'preferences' ? 'active':null }}" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                        @include('customer.Accounts._preferences')
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
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        $(document).ready(function () {

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
                    dataListView.draw();
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

            $('.datatables-basic').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('customer.invoices.search') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": 'responsive_id', orderable: false, searchable: false},
                    {"data": "uid"},
                    {"data": "uid"},
                    {"data": "id"},
                    {"data": "created_at"},
                    {"data": "type"},
                    {"data": "description"},
                    {"data": "amount"},
                    {"data": "status"},
                    {"data": "actions", orderable: false, searchable: false}
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
                        targets: 2,
                        visible: false
                    },

                    {
                        // Actions
                        targets: -1,
                        title: '{{ __('locale.labels.actions') }}',
                        orderable: false,
                        render: function (data, type, full) {
                            return (
                                '<a href="' + full['edit'] + '" class="text-primary">' +
                                feather.icons['eye'].toSvg({class: 'font-medium-4'}) +
                                '</a>'
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
                                return 'Details of ' + data['id'];
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
                order: [[2, "desc"]],
                displayLength: 10,
            });
            // On cancel
            $(".action-cancel").on("click", function (e) {
                e.stopPropagation();
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.subscription.cancel_subscription_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('locale.labels.cancel_it') }}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('customer.subscriptions.cancel', $subscription->uid) }}",
                            type: "POST",
                            data: {
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

            let credit = $('#credit');
            let credit_notify = $('#credit_notify');
            let creditWarning = $("#credit_warning");

            if (creditWarning.is(':checked') === false) {
                credit.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });

                credit_notify.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });


            }

            creditWarning.on('click', function () {
                credit.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });

                credit_notify.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });

            let endPeriodLastDays = $('#end_period_last_days');
            let subscriptionNotify = $('#subscription_notify');
            let subscriptionWarning = $("#subscription_warning");

            if (subscriptionWarning.is(':checked') === false) {
                endPeriodLastDays.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });

                subscriptionNotify.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });


            }

            subscriptionWarning.on('click', function () {
                endPeriodLastDays.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });

                subscriptionNotify.prop('disabled', function (i, v) {
                    $(this).removeAttr('value');
                    return !v;
                });
            });


        });


    </script>
@endsection
