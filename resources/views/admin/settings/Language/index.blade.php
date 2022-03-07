@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Language'))

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

        @can('new languages')
            <div class="action-btns d-none">
                <div class="btn-dropdown mr-1 mb-1 add-new-div">
                    <div class="btn-group dropdown actions-dropodown">
                        <a href="{{ route('admin.languages.create') }}" class="btn btn-white px-1 py-1 waves-effect waves-light text-primary text-bold-500"> {{__('locale.buttons.add_new')}} <i class="feather icon-plus-circle"></i></a>
                    </div>
                </div>

            </div>
        @endcan
        {{-- DataTable starts --}}
        <div class="table-responsive">
            <table class="table data-list-view">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('locale.labels.name')}}</th>
                    <th>{{__('locale.currencies.code')}}</th>
                    <th>{{__('locale.labels.status')}}</th>
                    <th>{{__('locale.labels.actions')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($languages as $lang)
                    <tr>
                        <td></td>
                        <td>{{ $lang->name }}</td>
                        <td>{{ strtoupper($lang->code) }}</td>
                        <td>
                            @if($lang->code != 'en')
                                <div class='custom-control custom-switch switch-lg custom-switch-success'>
                                    <input type="checkbox" class="custom-control-input get_status" id="{{ $lang->id }}" data-id="{{ $lang->id }}" name='status' @if($lang->status == true) checked @endif>
                                    <label class="custom-control-label" for="{{ $lang->id }}">
                                        <span class="switch-text-left">{{ __('locale.labels.active') }}</span>
                                        <span class="switch-text-right">{{ __('locale.labels.inactive') }}</span>
                                    </label>
                                </div>
                            @else
                                <div class="chip chip-success">
                                    <div class="chip-body">
                                        <div class="chip-text">{{ __('locale.labels.active') }}</div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            {{--                            <a href="{{ route('admin.languages.show', $lang->id) }}" class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title="{{__('locale.labels.translate')}}"> <i class="feather icon-external-link us-2x"></i></a>--}}
                            <a href="{{ route('admin.languages.upload', $lang->id) }}" class='text-info mr-1' data-toggle='tooltip' data-placement='top' title="{{__('locale.labels.upload')}}"> <i class="feather icon-upload us-2x"></i></a>
                            <a href="{{route('admin.languages.download', $lang->id)}}" class='text-success mr-1 action-download' data-toggle='tooltip' data-placement='top' title="{{__('locale.labels.download')}}"> <i class="feather icon-download us-2x"></i></a>
                            @if($lang->code != 'en')
                                <span class='action-delete text-danger' data-id="{{$lang->id}}" data-toggle='tooltip' data-placement='top' title="{{__('locale.buttons.delete')}}"><i class='feather icon-trash us-2x'></i></span>
                            @endif
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
                        newestOnTop: true,
                        timeout: 2000
                    });
                    dataListView.draw();
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

            // init list view datatable
            // init table dom
            let Table = $("table");

            let dataListView = $(".data-list-view").DataTable({

                "processing": true,
                "columns": [
                    {"data": "id", orderable: false, searchable: false},
                    {"data": "name"},
                    {"data": "code"},
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

            // To append actions dropdown before add new button
            let actionDropdown = $(".add-new-div")
            actionDropdown.insertBefore($(".top .actions .dt-buttons"))

            // Scrollbar
            if ($(".data-items").length > 0) {
                new PerfectScrollbar(".data-items", {wheelPropagation: false})
            }


            //change status
            Table.delegate(".get_status", "click", function () {
                let language_id = $(this).data('id');
                $.ajax({
                    url: "{{ url(config('app.admin_path').'/languages')}}" + '/' + language_id + '/active',
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
                            url: "{{ url(config('app.admin_path').'/languages')}}" + '/' + id,
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


        });

    </script>
@endsection
