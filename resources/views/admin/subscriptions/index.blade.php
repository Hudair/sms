@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Subscriptions'))

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection

@section('content')

    <!-- Basic table -->
    <section id="datatables-basic">
        <div class="mb-3 mt-2">
            @can('manage subscription')
                <div class="btn-group">
                    <button
                            class="btn btn-primary fw-bold dropdown-toggle"
                            type="button"
                            id="bulk_actions"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                    >
                        {{ __('locale.labels.actions') }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="bulk_actions">
                        <a class="dropdown-item bulk-cancel" href="#"><i data-feather="stop-circle"></i> {{ __('locale.datatables.bulk_cancel') }}</a>
                    </div>
                </div>
            @endcan

            @can('new subscription')
                <div class="btn-group">
                    <a href="{{route('admin.subscriptions.create')}}" class="btn btn-success waves-light waves-effect fw-bold mx-1"> {{__('locale.buttons.new_subscription')}} <i data-feather="plus-circle"></i></a>
                </div>
            @endcan

        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <table class="table datatables-basic">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>{{ __('locale.labels.id') }}</th>
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
            </div>
        </div>
    </section>
    <!--/ Basic table -->


@endsection


@section('vendor-script')
    {{-- vendor files --}}
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
    {{-- Page js files --}}
    <script>
        $(document).ready(function () {
            "use strict"

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

            // init table dom
            let Table = $("table");

            // init list view datatable
            let dataListView = $('.datatables-basic').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('admin.subscriptions.search') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": 'responsive_id', orderable: false, searchable: false},
                    {"data": "uid"},
                    {"data": "uid"},
                    {"data": "name"},
                    {"data": "subscribed_by"},
                    {"data": "start_at", orderable: false, searchable: false},
                    {"data": "end_at", orderable: false, searchable: false},
                    {"data": "status"},
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
                        // For Checkboxes
                        targets: 1,
                        orderable: false,
                        responsivePriority: 3,
                        render: function (data) {
                            return (
                                '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="" id="' +
                                data +
                                '" /><label class="form-check-label" for="' +
                                data +
                                '"></label></div>'
                            );
                        },
                        checkboxes: {
                            selectAllRender:
                                '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>',
                            selectRow: true
                        }
                    },
                    {
                        targets: 2,
                        visible: false
                    },
                    {
                        // Avatar image/badge, Name and post
                        targets: 4,
                        responsivePriority: 1,
                        render: function (data, type, full) {
                            let $user_img = full['avatar'],
                                $name = full['subscribed_by'],
                                $email = full['email'];
                            // For Avatar image
                            let $output = '<img src="' + $user_img + '" alt="Avatar" width="32" height="32">';

                            // Creates full output for row
                            return '<div class="d-flex justify-content-left align-items-center">' +
                                '<div class="avatar ' +
                                ' me-1">' +
                                $output +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate fw-bold">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $email +
                                '</small>' +
                                '</div>' +
                                '</div>';
                        }
                    },
                    {
                        // Actions
                        targets: -1,
                        title: '{{ __('locale.labels.actions') }}',
                        orderable: false,
                        render: function (data, type, full) {
                            var $return_url = '';

                            if (full['is_active'] === true) {
                                $return_url += '<span class="action-cancel text-warning px-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title=' + full['cancel_label'] + ' data-id=' + full['uid'] + '>' +
                                    feather.icons['stop-circle'].toSvg({class: 'font-medium-4'}) +
                                    '</span>';
                            }

                            if (full['is_ended'] === true) {
                                $return_url += '<span class="action-delete text-danger px-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title=' + full['delete_label'] + ' data-id=' + full['uid'] + '>' +
                                    feather.icons['trash'].toSvg({class: 'font-medium-4'}) +
                                    '</span>';
                            }

                            return (
                                $return_url +

                                '<a href="' + full['logs'] + '" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title=' + full['logs_label'] + '>' +
                                feather.icons['bar-chart-2'].toSvg({class: 'font-medium-4'}) +
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
                order: [[2, "desc"]],
                displayLength: 10,
            });

            Table.delegate(".action-cancel", 'click', function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
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

            // On Delete
            Table.delegate(".action-delete", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
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


            //Bulk cancel
            $(".bulk-cancel").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.subscription.cancel_subscriptions')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{__('locale.labels.cancel_selected')}}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let subscription_ids = [];
                        let rows_selected = dataListView.column(1).checkboxes.selected();

                        $.each(rows_selected, function (index, rowId) {
                            subscription_ids.push(rowId)
                        });

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
                        } else {
                            toastr['warning']("{{ __('locale.labels.at_least_one_data') }}", "{{ __('locale.labels.attention') }}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }

                    }
                })
            });

        });

    </script>
@endsection
