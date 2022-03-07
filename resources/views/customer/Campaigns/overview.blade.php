@extends('layouts.contentLayoutMaster')

@section('title', __('locale.menu.Overview'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">

@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/noui-slider.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-noui.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/pages/data-list-view.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
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


    <section id="nav-justified">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-fill mt-5" id="myTab2" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="settings-tab-justified"
                                       data-toggle="tab"
                                       href="#overview"
                                       role="tab"
                                       aria-controls="overview"
                                       aria-selected="true">
                                        <i class="feather icon-bar-chart primary"></i> {{ __('locale.menu.Overview') }}
                                    </a>
                                </li>

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
                            </ul>


                            {{-- Tab panes --}}
                            <div class="tab-content pt-1">
                                <div class="tab-pane active" id="overview" role="tabpanel" aria-labelledby="overview-tab-justified">
                                    @include('customer.Campaigns._overview')
                                </div>


                                <div class="tab-pane" id="contact" role="tabpanel" aria-labelledby="contact-tab-justified">
                                    @include('customer.Campaigns._contacts')
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

    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>

@endsection


@section('page-script')



    <script>
        $(document).ready(function () {


            $(window).on("load", function () {

                let $danger = '#EA5455';
                let $success = '#00db89';
                let $primary = '#7367f0';
                let $primary_light = '#9c8cfc';
                let $danger_light = '#f29292';
                let $stroke_color = '#b9c3cd';


                // Customer Chart
                // -----------------------------

                let Delivered = "{{ $campaign->readCache('DeliveredCount') }}";
                let Failed = "{{ $campaign->readCache('FailedDeliveredCount') }}";

                let customerChartoptions = {
                    chart: {
                        type: 'pie',
                        height: 325,
                        dropShadow: {
                            enabled: false,
                            blur: 5,
                            left: 1,
                            top: 1,
                            opacity: 0.2
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    labels: ['{{ __('locale.labels.delivered') }}', '{{ __('locale.labels.failed') }}'],
                    series: [parseInt(Delivered), parseInt(Failed)],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {show: true},
                    stroke: {
                        width: 5
                    },
                    colors: [$primary, $danger],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            gradientToColors: [$primary_light, $danger_light]
                        }
                    }
                }

                let customerChart = new ApexCharts(
                    document.querySelector("#customer-chart"),
                    customerChartoptions
                );

                customerChart.render();


                // Goal Overview  Chart
                // -----------------------------

                let goalChartoptions = {
                    chart: {
                        height: 250,
                        type: 'radialBar',
                        sparkline: {
                            enabled: true,
                        },
                        dropShadow: {
                            enabled: true,
                            blur: 3,
                            left: 1,
                            top: 1,
                            opacity: 0.1
                        },
                    },
                    colors: [$success],
                    plotOptions: {
                        radialBar: {
                            size: 110,
                            startAngle: -150,
                            endAngle: 150,
                            hollow: {
                                size: '77%',
                            },
                            track: {
                                background: $stroke_color,
                                strokeWidth: '50%',
                            },
                            dataLabels: {
                                name: {
                                    show: false
                                },
                                value: {
                                    offsetY: 18,
                                    color: $stroke_color,
                                    fontSize: '4rem'
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'horizontal',
                            shadeIntensity: 0.5,
                            gradientToColors: ['#00b5b5'],
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        },
                    },
                    series: [" {{ round($campaign->readCache('DeliveredCount') / $campaign->readCache('ContactCount') * 100)  }} "],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let goalChart = new ApexCharts(
                    document.querySelector("#goal-overview-chart"),
                    goalChartoptions
                );

                goalChart.render();


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
                    "url": "{{ route('customer.reports.campaign.reports', $campaign->uid) }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": "uid", orderable: false, searchable: false},
                    {"data": "created_at"},
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
                    url: "{{ url('/reports')}}" + '/' + id + '/view',
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
