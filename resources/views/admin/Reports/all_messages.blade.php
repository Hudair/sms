@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.SMS History'))

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

        <div class="col-12">
            <code>{{ __('locale.description.reports') }}</code>
        </div>

        <div class="action-btns d-none">
            <div class="btn-dropdown mr-1 mb-1 add-new-div">
                <div class="btn-group dropdown actions-dropodown">
                    <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('locale.labels.actions') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item bulk-delete" href="#"><i class="feather icon-trash"></i>{{ __('locale.datatables.bulk_delete') }}</a>
                    </div>
                </div>


                <div class="btn-group dropdown actions-dropodown">
                    <a href="{{ route('admin.reports.export') }}" class="btn btn-white px-1 py-1 waves-effect waves-light text-info text-bold-500"> {{__('locale.buttons.export')}} <i class="feather icon-file-text"></i></a>
                </div>


            </div>

        </div>

        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('locale.labels.date')}}</th>
                    <th>{{__('locale.menu.Customer')}} </th>
                    <th>{{__('locale.labels.direction')}} </th>
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
                    "url": "{{ route('admin.reports.search.all') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "uid", orderable: false, searchable: false},
                    {"data": "created_at"},
                    {"data": "user_id"},
                    {"data": "send_by"},
                    {"data": "sms_type"},
                    {"data": "from"},
                    {"data": "to"},
                    {"data": "cost"},
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

            // On view
            Table.delegate(".action-view", "click", function (e) {
                e.stopPropagation();
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ url(config('app.admin_path').'/reports')}}" + '/' + id + '/view',
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (data) {
                        Swal.fire({
                            html: `<div class="table-responsive">
<table class="table table-striped text-left">

        <tbody>
            <tr>
                <td>{{ __('locale.labels.from') }}</td>
                <td>` + data.data.from + `</td>
            </tr>
            <tr>
                <td>{{ __('locale.labels.to') }}</td>
                <td>` + data.data.to + `</td>
            </tr>
            <tr>
                <td>{{ __('locale.labels.message') }}</td>
                <td>` + data.data.message + `</td>
            </tr>
            <tr>
                <td>{{ __('locale.labels.type') }}</td>
                <td>` + data.data.sms_type + `</td>
            </tr>
            <tr>
                <td>{{ __('locale.labels.status') }}</td>
                <td>` + data.data.status + `</td>
            </tr>
            <tr>
                <td>{{ __('locale.labels.cost') }}</td>
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
                            url: "{{ url(config('app.admin_path').'/reports')}}" + '/' + id + '/destroy',
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

            //Bulk Delete
            $(".bulk-delete").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.campaigns.delete_sms')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('locale.labels.delete_selected')}}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let sms_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            sms_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (sms_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('admin.reports.batch_action') }}",
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
