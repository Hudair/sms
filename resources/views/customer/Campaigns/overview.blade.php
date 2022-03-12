@extends('layouts.contentLayoutMaster')

@section('title', __('locale.menu.Overview'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">

@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
@endsection

@section('content')
    <section class="campaign-overview">

        <div class="row">
            <div class="col-12">

                <ul class="nav nav-pills mb-2" role="tablist">
                    <!-- overview -->
                    <li class="nav-item">
                        <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#overview" aria-controls="overview" role="tab" aria-selected="true">
                            <i data-feather="pie-chart" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">{{__('locale.menu.Overview')}}</span>
                        </a>
                    </li>

                    <!-- contacts -->
                    <li class="nav-item">
                        <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" href="#contacts" aria-controls="contacts" role="tab" aria-selected="false">
                            <i data-feather="bar-chart" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">{{ __('locale.menu.SMS History') }}</span>
                        </a>
                    </li>

                </ul>


                <div class="tab-content">

                    <div class="tab-pane active" id="overview" aria-labelledby="overview-tab" role="tabpanel">
                        @include('customer.Campaigns._overview')
                    </div>

                    <div class="tab-pane" id="contacts" aria-labelledby="contacts-tab" role="tabpanel">
                        @include('customer.Campaigns._contacts')
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <!-- vendor files -->
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
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>

@endsection


@section('page-script')

    <script>
        $(document).ready(function () {

            $('#contacts-tab').on('click', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            });

            $(window).on("load", function () {

                let $success = '#00db89',
                    $strok_color = '#b9c3cd';


                // Customer Chart
                // -----------------------------

                let Delivered = "{{ $campaign->readCache('DeliveredCount') }}";
                let Failed = "{{ $campaign->readCache('FailedDeliveredCount') }}";

                let smsReports = {
                    chart: {
                        type: 'pie',
                        height: 285,
                        toolbar: {
                            show: false
                        }
                    },
                    labels: ["{{ __('locale.labels.delivered') }}", "{{ __('locale.labels.failed') }}"],
                    series: [parseInt(Delivered), parseInt(Failed)],
                    dataLabels: {
                        enabled: false
                    },
                    legend: {show: false},
                    stroke: {
                        width: 4
                    },
                    colors: ['#7367F0', '#EA5455'],
                }

                let customerChart = new ApexCharts(
                    document.querySelector("#sms-reports"),
                    smsReports
                );

                customerChart.render();


                // Goal Overview  Chart
                // -----------------------------

                let goalChartoptions = {
                    chart: {
                        height: 245,
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
                            offsetY: -10,
                            startAngle: -150,
                            endAngle: 150,
                            hollow: {
                                size: '77%'
                            },
                            track: {
                                background: $strok_color,
                                strokeWidth: '50%',
                            },
                            dataLabels: {
                                name: {
                                    show: false
                                },
                                value: {
                                    offsetY: 18,
                                    color: $strok_color,
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
                    grid: {
                        padding: {
                            bottom: 30
                        }
                    }

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
                    "url": "{{ route('customer.reports.campaign.reports', $campaign->uid) }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {"data": 'responsive_id', orderable: false, searchable: false},
                    {"data": "uid"},
                    {"data": "uid"},
                    {"data": "created_at"},
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
