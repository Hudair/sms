@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.billing'))


@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">

    <style>
        table.dataTable {
            border: none !important;
        }

        table.dataTable thead tr {
            background-color: #fff;
        }
    </style>

@endsection

@section('content')
    <section id="vertical-tabs">
        <div class="row match-height">
            <div class="col-12">
                <div class="card overflow-hidden">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column text-uppercase" role="tablist">

                                    <li class="nav-item">
                                        <a class="nav-link active" id="credit-used-tab" data-toggle="tab" aria-controls="credit-used" href="#credit-used" role="tab" aria-selected="true">
                                            {{ __('locale.labels.credits_used') }}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="invoice-tab" data-toggle="tab" aria-controls="invoice" href="#invoice" role="tab" aria-selected="true">
                                            {{ __('locale.labels.invoices') }}
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="subscriptions-tab"
                                           data-toggle="tab"
                                           aria-controls="subscriptions"
                                           href="#subscriptions" role="tab"
                                           aria-selected="false">
                                            {{ __('locale.menu.Subscriptions') }}
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a class="nav-link" id="preferences-tab" data-toggle="tab" aria-controls="preferences"
                                           href="#preferences" role="tab" aria-selected="false"> {{__('locale.labels.preferences')}} </a>
                                    </li>

                                </ul>

                                <div class="tab-content">

                                    <div class="tab-pane active" id="credit-used" role="tabpanel" aria-labelledby="credit-used-tab">
                                        @include('customer.Accounts._credits_used')
                                    </div>

                                    <div class="tab-pane" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                                        @include('customer.Accounts._invoices')
                                    </div>

                                    <div class="tab-pane" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                                        @include('customer.Accounts._subscriptions')
                                    </div>

                                    <div class="tab-pane" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                                        @include('customer.Accounts._preferences')
                                    </div>
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

    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.select.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection


@section('page-script')

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
                        newestOnTop: true,
                        timeOut: 3000
                    });

                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
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


            $('.data-list-view').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('customer.invoices.search') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "created_at"},
                    {"data": "uid"},
                    {"data": "type"},
                    {"data": "description"},
                    {"data": "amount"},
                    {"data": "status"},
                    {"data": "action", orderable: false, searchable: false}
                ],

                bAutoWidth: false,
                responsive: false,
                searchDelay: 1500,
                columnDefs: [
                    {
                        orderable: false,
                        targets: 0,
                        checkboxes: {selectRow: true}
                    }
                ],
                dom:
                    '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sZeroRecords: "{{ __('locale.datatables.no_results') }}",
                    sSearch: "",
                    sProcessing: "{{ __('locale.datatables.processing') }}",
                    oPaginate: {
                        sFirst: "{{ __('locale.datatables.first') }}",
                        sPrevious: "{{ __('locale.datatables.previous') }}",
                        sNext: "{{ __('locale.datatables.next') }}",
                        sLast: "{{ __('locale.datatables.last') }}"
                    }
                },
                aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
                select: {
                    style: "multi"
                },
                order: [[0, "desc"]],
                bInfo: false,
                pageLength: 10,
                buttons: [],
                initComplete: function () {
                    $(".dt-buttons .btn").removeClass("btn-secondary")
                }

            });

            // Scrollbar
            if ($(".data-items").length > 0) {
                new PerfectScrollbar(".data-items", {wheelPropagation: false})
            }

            // On cancel
            $(".action-cancel").on("click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.subscription.cancel_subscription_warning') }}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.cancel_it') }}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
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
