@extends('layouts.contentLayoutMaster')

@section('title', $user->displayName())

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/app-user.css')) }}">
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
    <!-- users edit start -->
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-justified mb-3" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                                <i class="feather icon-user mr-25"></i>{{__('locale.labels.account')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" aria-controls="security" role="tab" aria-selected="true">
                                <i class="feather icon-lock mr-25"></i>{{__('locale.labels.security')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="notification-tab" data-toggle="tab" href="#notification" aria-controls="notification" role="tab" aria-selected="false">
                                <i class="feather icon-bell mr-25"></i>{{__('locale.labels.notifications')}}
                            </a>
                        </li>


                        @if(config('app.two_factor') == true)
                            <li class="nav-item">
                                <a class="nav-link" id="two-factor-tab" data-toggle="tab" href="#two-factor" aria-controls="two-factor" role="tab" aria-selected="false">
                                    <i class="feather icon-log-in mr-25"></i>{{__('locale.labels.two_factor_authentication')}}
                                </a>
                            </li>
                        @endif

                        @if($user->active_portal == 'customer')
                            <li class="nav-item">
                                <a class="nav-link" id="information-tab" data-toggle="tab" href="#information" aria-controls="information" role="tab" aria-selected="false">
                                    <i class="feather icon-info mr-25"></i>{{__('locale.labels.information')}}
                                </a>
                            </li>
                        @endif


                    </ul>


                    <div class="tab-content">

                        <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
                            <!-- users edit media object start -->
                            <div class="media mb-2">
                                <a class="mr-2 my-25" href="{{ route('user.account') }}">
                                    <img src="{{ route('user.avatar') }}" alt="{{ $user->displayName() }}" class="users-avatar-shadow rounded" height="120" width="120">
                                </a>
                                <div class="media-body mt-50">
                                    <h4 class="media-heading">{{ $user->displayName() }}</h4>
                                    <div class="col-12 d-flex mt-1 px-0">
                                        @include('auth.profile._update_avatar')
                                        <span id="remove-avatar" data-id="{{$user->uid}}" class="btn btn-outline-danger d-none d-sm-block"><i class="feather icon-trash-2"></i> {{__('locale.labels.remove')}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- users edit media object ends -->

                            <!-- users edit account form start -->

                        @include('auth.profile._accounts')
                        <!-- users edit account form ends -->

                        </div>

                        <div class="tab-pane" id="security" aria-labelledby="security-tab" role="tabpanel">
                            <!-- users edit Info form start -->
                        @include('auth.profile._security')
                        <!-- users edit Info form ends -->
                        </div>

                        <div class="tab-pane" id="notification" aria-labelledby="notification-tab" role="tabpanel">
                            <!-- users edit Info form start -->
                        @include('auth.profile._notifications')
                        <!-- users edit Info form ends -->
                        </div>


                        @if($user->active_portal == 'customer')
                            <div class="tab-pane" id="information" aria-labelledby="information-tab" role="tabpanel">
                                <!-- users edit Info form start -->
                            @include('auth.profile._information')
                            <!-- users edit Info form ends -->
                            </div>
                        @endif


                        @if(config('app.two_factor') == true)
                            <div class="tab-pane" id="two-factor" aria-labelledby="two-factor-tab" role="tabpanel">
                                @include('auth.profile._two_factor_authentication')
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- users edit ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
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
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/navs/navs.js')) }}"></script>

    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);
            let showHideInput = $('.show_hide_password input');
            let showHideIcon = $('.show_hide_password i');

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }


            // Basic Select2 select
            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
            });


            $(".form-control-position").on('click', function (event) {
                event.preventDefault();
                if (showHideInput.attr("type") === "text") {
                    showHideInput.attr('type', 'password');
                    showHideIcon.addClass("icon-eye-off");
                    showHideIcon.removeClass("icon-eye");
                } else if (showHideInput.attr("type") === "password") {
                    showHideInput.attr('type', 'text');
                    showHideIcon.removeClass("icon-eye-off");
                    showHideIcon.addClass("icon-eye");
                }
            });


            //show response message
            function showResponseMessage(data) {

                if (data.status === 'success') {
                    toastr.success(data.message, 'Success!!', {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true,
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
                    "url": "{{ route('user.account.notifications') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "uid", orderable: false, searchable: false},
                    {"data": "notification_type"},
                    {"data": "message"},
                    {"data": "mark_read", orderable: false, searchable: false},
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
                            url: "{{ url('account/notifications/')}}" + '/' + id + '/delete',
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

            //Bulk Read
            $(".bulk-read").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Yes! Mark read",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let notification_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            notification_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (notification_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('user.account.notifications.batch_action') }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'read',
                                    ids: notification_ids
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

            //Bulk Delete
            $(".bulk-delete").on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
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
                        let notification_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            notification_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (notification_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('user.account.notifications.batch_action') }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'destroy',
                                    ids: notification_ids
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


            Table.delegate(".get_status", "click", function () {
                let notification_id = $(this).data('id');
                $.ajax({
                    url: "{{ url('account/notifications/')}}" + '/' + notification_id + '/active',
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    success: function (data) {
                        showResponseMessage(data);
                    }
                });
            });


            // On Remove Avatar
            $('#remove-avatar').on("click", function (e) {

                e.stopPropagation();
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
                            url: "{{ route('user.remove_avatar') }}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                showResponseMessage(data);
                                setTimeout(function () {
                                    location.reload();
                                }, 5000);
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
