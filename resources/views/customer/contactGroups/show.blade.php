@extends('layouts.contentLayoutMaster')

@section('title', $contact->name)

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">

@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/noui-slider.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-noui.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">

    <style>
        .settings .select2-container--classic .select2-selection--single {
            border-left: 0;
            border-radius: 0 4px 4px 0;
        }

        table.dataTable {
            border: none !important;
        }

        table.dataTable thead tr {
            background-color: #fff;
        }

        .select2-dropdown {
            z-index: 1061;
        }
    </style>

@endsection

@section('content')


    <section id="nav-justified">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-fill mt-5" id="myTab2" role="tablist">

                                @can('update_contact_group')
                                    <li class="nav-item">
                                        <a class="nav-link active"
                                           id="settings-tab-justified"
                                           data-toggle="tab"
                                           href="#settings"
                                           role="tab"
                                           aria-controls="settings"
                                           aria-selected="true">
                                            <i class="feather icon-settings primary"></i> {{ __('locale.labels.settings') }}
                                        </a>
                                    </li>
                                @endcan

                                {{-- contact --}}
                                @can('view_contact')
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="contact-tab-justified"
                                           data-toggle="tab"
                                           href="#contact"
                                           role="tab"
                                           aria-controls="contact"
                                           aria-selected="true">
                                            <i class="feather icon-users primary"></i> {{ __('locale.contacts.contacts') }}
                                        </a>
                                    </li>
                                @endcan

                                {{-- settings --}}
                                @can('update_contact_group')

                                    {{-- message --}}
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="message-tab-justified"
                                           data-toggle="tab"
                                           href="#message"
                                           role="tab"
                                           aria-controls="message"
                                           aria-selected="true">
                                            <i class="feather icon-message-circle primary"></i> {{ __('locale.labels.message') }}
                                        </a>
                                    </li>

                                    {{-- opt in keywords --}}
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="opt_in_keywords-tab-justified"
                                           data-toggle="tab"
                                           href="#opt_in_keywords"
                                           role="tab"
                                           aria-controls="opt_in_keywords"
                                           aria-selected="true">
                                            <i class="feather icon-user-check primary"></i> {{ __('locale.contacts.opt_in_keywords') }}
                                        </a>
                                    </li>

                                    {{-- opt out keywords --}}
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="opt_out_keywords-tab-justified"
                                           data-toggle="tab"
                                           href="#opt_out_keywords"
                                           role="tab"
                                           aria-controls="opt_out_keywords"
                                           aria-selected="true">
                                            <i class="feather icon-user-minus primary"></i> {{ __('locale.contacts.opt_out_keywords') }}
                                        </a>
                                    </li>
                                @endcan

                                {{-- import history --}}
                                @can('create_contact_group')
                                    <li class="nav-item">
                                        <a class="nav-link"
                                           id="import-history-tab-justified"
                                           data-toggle="tab"
                                           href="#import-history"
                                           role="tab"
                                           aria-controls="import-history"
                                           aria-selected="true">
                                            <i class="feather icon-pie-chart primary"></i> {{ __('locale.contacts.import_history') }}
                                        </a>
                                    </li>
                                @endcan

                            </ul>


                            {{-- Tab panes --}}
                            <div class="tab-content pt-1">
                                @can('update_contact_group')
                                    <div class="tab-pane active" id="settings" role="tabpanel" aria-labelledby="settings-tab-justified">
                                        @include('customer.contactGroups._settings')
                                    </div>
                                @endcan

                                {{-- cotnacts --}}
                                @can('view_contact')
                                    <div class="tab-pane" id="contact" role="tabpanel" aria-labelledby="contact-tab-justified">
                                        @include('customer.contactGroups._contacts')
                                    </div>
                                @endcan

                                {{-- settings --}}
                                @can('update_contact_group')
                                    {{-- message --}}
                                    <div class="tab-pane" id="message" role="tabpanel" aria-labelledby="message-tab-justified">
                                        @include('customer.contactGroups._message')
                                    </div>

                                    {{-- opt in keywords --}}
                                    <div class="tab-pane" id="opt_in_keywords" role="tabpanel" aria-labelledby="opt_in_keywords-tab-justified">
                                        @include('customer.contactGroups._opt_in_keywords')
                                    </div>

                                    {{-- opt in out keywords --}}
                                    <div class="tab-pane" id="opt_out_keywords" role="tabpanel" aria-labelledby="opt_out_keywords-tab-justified">
                                        @include('customer.contactGroups._opt_out_keywords')
                                    </div>
                                @endcan

                                {{-- import history --}}
                                @can('create_contact_group')
                                    <div class="tab-pane" id="import-history" role="tabpanel" aria-labelledby="import-history-tab-justified">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead class="thead-primary">
                                                <tr>
                                                    <th scope="col">{{ __('locale.labels.submitted') }}</th>
                                                    <th scope="col">{{ __('locale.labels.status') }}</th>
                                                    <th scope="col">{{ __('locale.labels.message') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse ($import_history as $history)
                                                    <tr>
                                                        <td> {{ \App\Library\Tool::customerDateTime($history->created_at) }} </td>
                                                        <td> {!! $history->getStatus() !!} </td>
                                                        <td>{{ $history->getOption('message') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center" colspan="5">
                                                            {{ __('locale.datatables.no_results') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endcan


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
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/wNumb.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/nouislider.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.select.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

    <script src="{{ asset(mix('js/scripts/extensions/copy-to-clipboard.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        $(document).ready(function () {

            $(".sender_id").on("click", function () {
                $("#sender_id").prop("disabled", !this.checked);
                $("#phone_number").prop("disabled", this.checked);
            });

            $(".phone_number").on("click", function () {
                $("#phone_number").prop("disabled", !this.checked);
                $("#sender_id").prop("disabled", this.checked);
            });

            let $get_msg = $("#text_message"),
                merge_state = $('#available_tag');

            merge_state.on('change', function () {
                const caretPos = $get_msg[0].selectionStart;
                const textAreaTxt = $get_msg.val();
                let txtToAdd = this.value;
                if (txtToAdd) {
                    txtToAdd = '{' + txtToAdd + '}';
                }

                $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
            });

            $("#message_form").on('change', function () {
                let smsForm = $(this).val();
                let showSubscribeURL = $('.show-subscribe-url');

                if (smsForm === 'signup_sms') {
                    showSubscribeURL.show();
                } else {
                    showSubscribeURL.hide();
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('customer.contacts.message_form', $contact->uid) }}",
                    data: {
                        _token: "{{csrf_token()}}",
                        sms_form: smsForm
                    },
                    cache: false,
                    success: function (data) {
                        $get_msg.val(data.message).val();
                    }
                });
            });

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

            $(".select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                theme: "classic"
            });

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }


            // init table dom
            let Table = $("table");

            // init list view datatable
            let dataListView = $('.data-list-view').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('customer.contact.search', $contact->uid) }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "uid", orderable: false, searchable: false},
                    {"data": "phone"},
                    {"data": "name"},
                    {"data": "created_at", orderable: false, searchable: false},
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

            $(".opt-in-keywords").DataTable({
                "processing": true,
                "columns": [
                    {"data": "keyword"},
                    {"data": "created_at", orderable: false, searchable: false},
                    {"data": "action", orderable: false, searchable: false}
                ],
                bAutoWidth: false,
                responsive: false,
                dom:
                    '<"top"<"actions optin-keywords action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
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
            let addNewDropdown = $(".add-new-keyword")
            addNewDropdown.insertBefore($('.top .optin-keywords .dt-buttons'))


            $(".opt-out-keywords").DataTable({
                "processing": true,
                "columns": [
                    {"data": "keyword"},
                    {"data": "created_at", orderable: false, searchable: false},
                    {"data": "action", orderable: false, searchable: false}
                ],
                bAutoWidth: false,
                responsive: false,
                dom:
                    '<"top"<"actions optout-keywords action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
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
            let optOutDropdown = $(".add-opt-out-keyword")
            optOutDropdown.insertBefore($('.top .optout-keywords .dt-buttons'))


            //change status
            Table.delegate(".get_status", "click", function () {
                let contact_id = $(this).data('id');
                let url = "{{ route('customer.contact.status', [ 'contact' => $contact->uid, 'id' => "contact_id"]) }}";

                $.ajax({
                    url: url.replace("contact_id", contact_id),
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

                let contact_id = $(this).data('id');
                let url = "{{ route('customer.contact.delete', [ 'contact' => $contact->uid, 'id' => "contact_id"]) }}";

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
                            url: url.replace("contact_id", contact_id),
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

            //Bulk subscribe
            $(".bulk-subscribe").on('click', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.contacts.subscribe_contacts')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('locale.labels.subscribe')}}",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let contacts_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            contacts_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (contacts_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('customer.contact.batch_action', $contact->uid) }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'subscribe',
                                    ids: contacts_ids
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
                            toastr.warning("{{ __('locale.labels.at_least_one_data') }}", "{{ __('locale.labels.attention') }}", {
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

            //bulk-unsubscribe
            $(".bulk-unsubscribe").on('click', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    text: "{{__('locale.contacts.unsubscribe_contacts')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('locale.labels.unsubscribe')}}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let contact_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            contact_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (contact_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('customer.contact.batch_action', $contact->uid) }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'unsubscribe',
                                    ids: contact_ids
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
                            toastr.warning("{{ __('locale.labels.at_least_one_data') }}", "{{ __('locale.labels.attention') }}", {
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

            //Bulk copy
            $(".bulk-copy").on('click', function (e) {
                e.preventDefault();

                let array = {!! $contact_groups !!}, options;
                $.each(array, function (key, value) {
                    options = `${options}<option value="${value.uid}">${value.name}</option>`;
                });

                let html = `<select id="my-select2">${options}</select>`;

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    html: html,
                    onOpen: function () {
                        $('#my-select2').select2({
                            dropdownAutoWidth: true,
                            width: '100%',
                            theme: "classic"
                        });
                    },
                    preConfirm: function () {
                        return $('#my-select2').val()
                    },

                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.copy') }}",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let contact_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            contact_ids.push(dataListView.row(rowIdx).data().uid)
                        })
                        if (contact_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('customer.contact.batch_action', $contact->uid) }}",
                                type: "POST",
                                data: {
                                    _method: 'POST',
                                    action: 'copy',
                                    ids: contact_ids,
                                    target_group: result.value,
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
                        } else {
                            toastr.warning("{{ __('locale.labels.at_least_one_data') }}", "{{ __('locale.labels.attention') }}", {
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

            //Bulk move
            $(".bulk-move").on('click', function (e) {
                e.preventDefault();

                let array = {!! $contact_groups !!}, options;
                $.each(array, function (key, value) {
                    options = `${options}<option value="${value.uid}">${value.name}</option>`;
                });

                let html = `<select id="my-select2">${options}</select>`;

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    html: html,
                    onOpen: function () {
                        $('#my-select2').select2({
                            dropdownAutoWidth: true,
                            width: '100%',
                            theme: "classic"
                        });
                    },
                    preConfirm: function () {
                        return $('#my-select2').val()
                    },

                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.move') }}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        let contact_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            contact_ids.push(dataListView.row(rowIdx).data().uid)
                        })
                        if (contact_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('customer.contact.batch_action', $contact->uid) }}",
                                type: "POST",
                                data: {
                                    _method: 'POST',
                                    action: 'move',
                                    ids: contact_ids,
                                    target_group: result.value,
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
                        } else {
                            toastr.warning("{{ __('locale.labels.at_least_one_data') }}", "{{ __('locale.labels.attention') }}", {
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
                    text: "{{__('locale.contacts.delete_contacts')}}",
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
                        let contact_ids = [];
                        dataListView.rows('.selected').every(function (rowIdx) {
                            contact_ids.push(dataListView.row(rowIdx).data().uid)
                        })

                        if (contact_ids.length > 1) {

                            $.ajax({
                                url: "{{ route('customer.contact.batch_action', $contact->uid) }}",
                                type: "POST",
                                data: {
                                    _token: "{{csrf_token()}}",
                                    action: 'destroy',
                                    ids: contact_ids
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

            //add opt in keyword
            $(".add_opt_in_keyword").on('click', function (e) {
                e.preventDefault();

                let remainOptinKeywords = {!! $remain_opt_in_keywords !!}, options;
                $.each(remainOptinKeywords, function (key, value) {
                    options = `${options}<option value="${value.keyword_name}">${value.keyword_name}</option>`;
                });

                let html = `<select id="opt-in-select2">${options}</select>`;

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    html: html,
                    onOpen: function () {
                        $('#opt-in-select2').select2({
                            dropdownAutoWidth: true,
                            width: '100%',
                            theme: "classic"
                        });
                    },
                    preConfirm: function () {
                        return $('#opt-in-select2').val()
                    },

                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.yes') }}",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('customer.contacts.optin_keyword', $contact->uid) }}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                keyword_name: result.value,
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

            //add opt out keyword
            $(".add_opt_out_keyword").on('click', function (e) {
                e.preventDefault();

                let remainOptOutKeywords = {!! $remain_opt_out_keywords !!}, options;
                $.each(remainOptOutKeywords, function (key, value) {
                    options = `${options}<option value="${value.keyword_name}">${value.keyword_name}</option>`;
                });

                let html = `<select id="opt-out-select2">${options}</select>`;

                Swal.fire({
                    title: "{{__('locale.labels.are_you_sure')}}",
                    html: html,
                    onOpen: function () {
                        $('#opt-out-select2').select2({
                            dropdownAutoWidth: true,
                            width: '100%',
                            theme: "classic"
                        });
                    },
                    preConfirm: function () {
                        return $('#opt-out-select2').val()
                    },

                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.yes') }}",
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('customer.contacts.optout_keyword', $contact->uid) }}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                keyword_name: result.value,
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
            Table.delegate(".action-delete-optin-keyword", "click", function (e) {

                e.stopPropagation();

                let keyword_id = $(this).data('id');
                let url = "{{ route('customer.contacts.delete_optin_keyword', [ 'contact' => $contact->uid, 'id' => "keyword_id"]) }}";

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
                            url: url.replace("keyword_id", keyword_id),
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
            Table.delegate(".action-delete-optout-keyword", "click", function (e) {

                e.stopPropagation();

                let keyword_id = $(this).data('id');
                let url = "{{ route('customer.contacts.delete_optout_keyword', [ 'contact' => $contact->uid, 'id' => "keyword_id"]) }}";

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
                            url: url.replace("keyword_id", keyword_id),
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


        });


    </script>
@endsection
