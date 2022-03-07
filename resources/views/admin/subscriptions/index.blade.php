@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Subscriptions'))

@section('vendor-style')
    {{-- vendor files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
    {{-- Data list view starts --}}
    <section id="data-list-view" class="data-list-view-header">
        <div class="action-btns d-none">
            <div class="btn-dropdown mr-1 mb-1 add-new-div">

                @can('manage subscription')
                    <div class="btn-group dropdown actions-dropodown">
                        <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('locale.labels.actions') }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item bulk-cancel" href="#"><i class="feather icon-stop-circle"></i>{{ __('locale.datatables.bulk_cancel') }}</a>
                        </div>
                    </div>
                @endcan

                @can('new subscription')
                    <div class="btn-group dropdown actions-dropodown">
                        <a href="{{route('admin.subscriptions.create')}}" class="btn btn-white px-1 py-1 waves-effect waves-light text-success text-bold-500"> {{__('locale.buttons.new_subscription')}} <i class="feather icon-plus-circle"></i></a>
                    </div>
                @endcan

            </div>

        </div>

        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('locale.labels.name')}} </th>
                    <th>{{__('locale.subscription.subscribed_by')}}</th>
                    <th>{{__('locale.subscription.subscribed_on')}}</th>
                    <th>{{__('locale.subscription.ended_at')}}</th>
                    <th>{{__('locale.labels.status')}}</th>
                    <th>{{__('locale.labels.actions')}}</th>
                </tr>
                </thead>
            </table>
        </div>
        {{-- DataTable ends --}}

    </section>
    <br>
    {{-- Data list view end --}}
@endsection
@section('vendor-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.select.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('page-script')

    <script>

        // init list view datatable
        $(document).ready(function () {
            "use strict"

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
                    dataListView.draw();
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

            // init table dom
            let Table = $("table");

            // init list view datatable
            let dataListView = $('.data-list-view').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('admin.subscriptions.search') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "uid", orderable: false, searchable: false},
                    {"data": "name"},
                    {"data": "subscribed_by"},
                    {"data": "start_at", orderable: false, searchable: false},
                    {"data": "end_at", orderable: false, searchable: false},
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

            dataListView.on('draw.dt', function () {
                setTimeout(function () {
                    if (navigator.userAgent.indexOf("Mac OS X") !== -1) {
                        $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox")
                    }
                }, 50);
            });


            // To append actions dropdown before add new button
            let actionDropdown = $(".add-new-div")
            actionDropdown.insertBefore($(".top .actions .dt-buttons"))

            // Scrollbar
            if ($(".data-items").length > 0) {
                new PerfectScrollbar(".data-items", {wheelPropagation: false})
            }

            // On cancel
            Table.delegate(".action-cancel", 'click', function (e) {
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
                            url: "{{ url(config('app.admin_path').'/subscriptions')}}" + '/' + id + '/cancel',
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


            // On Delete
            Table.delegate(".action-delete", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.labels.able_to_revert') }}",
                    type: 'warning',
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
                            url: "{{ url(config('app.admin_path').'/subscriptions')}}" + '/' + id,
                            type: "POST",
                            data: {
                                _method: 'DELETE',
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

            //Bulk cancel
            $(".bulk-cancel").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.subscription.cancel_subscriptions')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('locale.labels.cancel_selected')}}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let subscription_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            subscription_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (subscription_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('admin.subscriptions.batch_action') }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'cancel',
                                    ids: subscription_ids
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
                        } else {
                            toastr.warning("{{__('locale.labels.at_least_one_data')}}", "{{__('locale.labels.attention')}}", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        }

                    }
                })
            });
        });
    </script>

@endsection
