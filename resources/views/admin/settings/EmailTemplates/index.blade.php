@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Email Templates'))

@section('vendor-style')
    {{-- vendor files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">
@endsection

@section('content')
    {{-- Data list view starts --}}
    <section id="data-list-view" class="data-list-view-header">

        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('locale.labels.name')}}</th>
                    <th>{{__('locale.labels.status')}}</th>
                    <th>{{__('locale.labels.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($email_templates as $template)
                    <tr>
                        <td></td>
                        <td>{{ $template->name }}</td>
                        <td>
                            <div class='custom-control custom-switch switch-lg custom-switch-success'>
                                <input type="checkbox" class="custom-control-input get_status" id="{{ $template->uid }}" data-id="{{ $template->uid }}" name='status' @if($template->status == true) checked @endif>
                                <label class="custom-control-label" for="{{ $template->uid }}">
                                    <span class="switch-text-left">{{ __('locale.labels.active') }}</span>
                                    <span class="switch-text-right">{{ __('locale.labels.inactive') }}</span>
                                </label>
                            </div>
                        </td>
                        <td>
                            @can('update payment_gateways')
                                <a href="{{ route('admin.email-templates.show', $template->uid) }}" class='text-primary mr-1'><i class="feather icon-settings"></i> {{__('locale.buttons.update')}}</a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
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

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection
@section('page-script')
    <script>
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

            // init list view datatable
            // init table dom
            let Table = $("table");

            let dataListView = $(".data-list-view").DataTable({

                "processing": true,
                "columns": [
                    {"data": "id", orderable: false, searchable: false},
                    {"data": "name"},
                    {"data": "status"},
                    {"data": "action", orderable: false, searchable: false}
                ],
                bAutoWidth: false,
                responsive: false,
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
                order: [[0, "asc"]],
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


            //change status
            Table.delegate(".get_status", "click", function () {
                let template = $(this).data('id');

                $.ajax({
                    url: "{{ url(config('app.admin_path').'/email-templates')}}" + '/' + template + '/active',
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (data) {
                        showResponseMessage(data);
                    }
                });
            });

        });

    </script>
@endsection
