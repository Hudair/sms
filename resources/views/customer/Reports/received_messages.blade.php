@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Received Messages'))

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
            @if(Auth::user()->customer->getOption('delete_sms_history') == 'yes')
                <div class="btn-group">
                    <button
                            class="btn btn-primary fw-bold dropdown-toggle me-1"
                            type="button"
                            id="bulk_actions"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                    >
                        {{ __('locale.labels.actions') }}
                    </button>
                    <div class="dropdown-menu" aria-labelledby="bulk_actions">
                        <a class="dropdown-item bulk-delete" href="#"><i data-feather="trash"></i> {{ __('locale.datatables.bulk_delete') }}</a>
                    </div>
                </div>
            @endif

            @if(Auth::user()->customer->getOption('list_export') == 'yes')
                    <div class="btn-group">
                        <a href="{{ route('customer.reports.export.receive') }}" class="btn btn-info waves-light waves-effect fw-bold"> {{__('locale.buttons.export')}} <i data-feather="file-text"></i></a>
                    </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <table class="table datatables-basic">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{__('locale.labels.date')}}</th>
                            <th>{{__('locale.labels.type')}} </th>
                            <th>{{__('locale.labels.from')}}</th>
                            <th>{{__('locale.labels.to')}}</th>
                            <th>{{__('locale.labels.cost')}}</th>
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
                    "url": "{{ route('customer.reports.search.received') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": 'responsive_id', orderable: false, searchable: false},
                    {"data": "uid"},
                    {"data": "uid"},
                    {"data": "created_at"},
                    {"data": "sms_type"},
                    {"data": "from"},
                    {"data": "to"},
                    {"data": "cost"},
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
                        // Actions
                        targets: -1,
                        title: '{{ __('locale.labels.actions') }}',
                        orderable: false,
                        render: function (data, type, full) {
                            return (
                                '<span class="action-delete text-danger pe-1 cursor-pointer" data-id=' + full['uid'] + '>' +
                                feather.icons['trash'].toSvg({class: 'font-medium-4'}) +
                                '</span>' +
                                '<span class="action-view text-primary pe-1 cursor-pointer" data-id=' + full['uid'] + '>' +
                                feather.icons['eye'].toSvg({class: 'font-medium-4'}) +
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
                                return 'Details of ' + data['uid'];
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

            // On view
            Table.delegate(".action-view", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ url('/reports')}}" + '/' + id + '/view',
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (data) {
                        Swal.fire({
                            html: `<div class="table-responsive">
<table class="table table-striped">

        <tbody>
            <tr>
                <td width="35%">{{ __('locale.labels.from') }}</td>
                <td>` + data.data.from + `</td>
            </tr>
            <tr>
                <td width="35%">{{ __('locale.labels.to') }}</td>
                <td>` + data.data.to + `</td>
            </tr>
            <tr>
                <td width="35%">{{ __('locale.labels.message') }}</td>
                <td>` + data.data.message + `</td>
            </tr>
            <tr>
                <td width="35%">{{ __('locale.labels.type') }}</td>
                <td>` + data.data.sms_type + `</td>
            </tr>
            <tr>
                <td width="35%">{{ __('locale.labels.status') }}</td>
                <td>` + data.data.status + `</td>
            </tr>
            <tr>
                <td width="35%">{{ __('locale.labels.cost') }}</td>
                <td>` + data.data.cost + `</td>
            </tr>

</tbody>
</table>
</div>
`
                        })
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
                            url: "{{ url('/reports')}}" + '/' + id + '/destroy',
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
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                                }
                            }
                        })
                    }
                })
            });

            //Bulk Delete
            $(".bulk-delete").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.campaigns.delete_sms')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{__('locale.labels.delete_selected')}}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let sms_ids = [];
                        let rows_selected = dataListView.column(1).checkboxes.selected();

                        $.each(rows_selected, function (index, rowId) {
                            sms_ids.push(rowId)
                        });

                        if (sms_ids.length > 0) {

                            $.ajax({
                                url: "{{ route('customer.reports.batch_action') }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'destroy',
                                    ids: sms_ids
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
                                            closeButton: true,
                                            positionClass: 'toast-top-right',
                                            progressBar: true,
                                            newestOnTop: true,
                                            rtl: isRtl
                                        });
                                    }
                                }
                            })
                        } else {
                            toastr['warning']("{{__('locale.labels.at_least_one_data')}}", "{{__('locale.labels.attention')}}", {
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
