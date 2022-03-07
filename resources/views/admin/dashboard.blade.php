@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether-theme-arrows.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/tether.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/shepherd-theme-default.css')) }}">

@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/tour/tour.css')) }}">
@endsection



@section('content')

    <section>
        <div class="row">

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ \App\Models\User::where('is_customer', 1)->count() }}</h2>
                            <p>{{ __('locale.menu.Customers') }}</p>
                        </div>
                        <div class="avatar bg-rgba-primary p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-users text-primary font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ \App\Models\Plan::count() }}</h2>
                            <p>{{ __('locale.menu.Plan') }}</p>
                        </div>
                        <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-credit-card text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ \App\Models\Reports::count() }}</h2>
                            <p>{{ __('locale.labels.sms_send') }}</p>
                        </div>
                        <div class="avatar bg-rgba-danger p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-message-square text-danger font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ \App\Models\Campaigns::count() }}</h2>
                            <p>{{ __('locale.labels.campaigns_send') }}</p>
                        </div>
                        <div class="avatar bg-rgba-info p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-send text-info font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.customers_growth') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div id="customer-growth"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.sms_reports') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div id="sms-reports"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.revenue_this_month') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div id="revenue-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('locale.labels.recent_sender_id_requests') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 15%">{{ __('locale.labels.sender_id') }}</th>
                                    <th>{{ __('locale.labels.name') }}</th>
                                    <th>{{ __('locale.menu.Customer') }}</th>
                                    <th>{{ __('locale.plans.price') }}</th>
                                    <th>{{ __('locale.plans.validity') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($sender_ids as $senderid)
                                    <tr>
                                        <td><a href="{{ route('admin.senderid.show', $senderid->uid) }}">{{ $senderid->uid }}</a></td>
                                        <td>{{ $senderid->sender_id }}</td>
                                        <td><a href={{route('admin.customers.show', $senderid->user->uid)}}>{{ $senderid->user->displayName() }}</a></td>
                                        <td>{{ \App\Library\Tool::format_price($senderid->price, $senderid->currency->format) }}</td>
                                        <td>{{ $senderid->displayFrequencyTime() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.outgoing_sms_history_of_current_month') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div id="sms-outbound"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.incoming_sms_history_of_current_month') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body pb-0">
                            <div id="sms-inbound"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/tether.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/shepherd.min.js')) }}"></script>
@endsection

@section('page-script')

    <script>

    let tour = new Shepherd.Tour({
        classes: 'card',
        scrollTo: true,
        useModalOverlay: true,
        isCentered: true,
    })

    // tour steps
    tour.addStep('step-1', {
        text: 'Thank you for choosing Ultimate SMS!<br><br>' +
            'This small tour will guide you through some of the Ultimate SMS  features and will help you get started using the application.<br><br>' +
            'If you ever need support, please send email to <code>akasham67@gmail.com</code><br><br>' +
            'Let\'s get started!',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-2', {
        text: 'In order to make things easier to manage, Ultimate SMS is divided into several small sub-apps, like the <code>backend, customer, api</code><br><br>' +
            'The <code>backend</code> app is used for administrative tasks and here only the system users have access. You can create plan, customers, subscriptions, etc<br><br>' +
            'The <code>customer</code> app is used to create manage phone lists, subscribers, campaigns , and many more.<br><br>' +
            'The <code>api</code> app is used to allow custom integrations from various other apps with your own app, like customers sending sms from external systems to their lists. You can disable it any any time!<br><br>' +
            'To run ultimate sms, first, you need to create a <code>Sending Server</code>, then a <code>Plan</code>, after that, assign your created sending server on the plan. Finally, create a <code>customer</code> and assign the created plan.<br><br>' +
            'Click on next then you will find all details',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-3', {
        text: 'Maybe the most important thing that you have to do after you install the application is to make sure all the <code>cron jobs</code> are set properly. This is very important since without the cron jobs, the application will not be able to send any sms at all, or to do import contacts and a lot other tasks. <br><br>' +
            'For more details please go <code>Settings -> All Settings</code> menu and click on <code>Cron Jobs</code> tab',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-4', {
        text: '<code>Sending Servers</code> are needed in order to send out all emails from the application.<br><br>' +
            'To add a sending server please go <code>Sending -> Sending Servers</code> menu and click on <code>Add New Server</code> button.<br><br>' +
            'Finally, search your sending server and update your credentials.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-5', {
        text: 'After creating a sending server you need to create a <code>Plan</code>. Where you can set your <code>plan Price</code>, <code>SMS Limit</code>, <code>Assign Sending Sending servers</code>, and <code>all other features</code><br><br>' +
            'To create a Plan please go <code>Plan -> Plans</code> menu and click on <code>Add New</code> button.<br><br>' +
            'Finally, update your all features and settings of your plan.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });
    tour.addStep('step-6', {
        text: 'When you first installed the application, you were asked to create a customer account.<br><br>' +
            'In case you haven\'t done so, please go ahead and create one from <code>Customer -> Customers</code> menu and click on <code>Add New</code> button.<br><br>' +
            'Finally, insert your all details and assign your created plan to your customer.',
        buttons: [

            {
                text: "Skip",
                action: tour.complete
            },

            {
                text: "previous",
                action: tour.back
            },
            {
                text: 'Next',
                action: tour.next
            },
        ]
    });

    tour.addStep('step-7', {
        text: 'Ultimate SMS is flexible and modern. Please check all features perfectly then you will find all details about ultimate sms.',
        buttons: [
            {
                text: "previous",
                action: tour.back
            },

            {
                text: "Finish",
                action: tour.complete
            },
        ]
    });

    function dismissTour() {
        if (!localStorage.getItem('shepherd-tour')) {
            localStorage.setItem('shepherd-tour', 'yes');
        }
    }

    tour.on('complete', dismissTour);

    // function to remove tour on small screen
    function displayTour() {
        window.resizeEvt;
        if ($(window).width() > 576) {
            clearTimeout(window.resizeEvt);

            // Initiate the tour
            if (!localStorage.getItem('shepherd-tour')) {
                tour.start();
            }
        } else {
            clearTimeout(window.resizeEvt);
            tour.cancel()
            window.resizeEvt = setTimeout(function () {
                alert("Tour only works for large screens!");
            }, 250);
        }
    }


    if (!localStorage.getItem('shepherd-tour')) {
        displayTour();
        $(window).resize(displayTour)
    }


        $(window).on("load", function () {

            let $primary = '#7367F0';
            let $strok_color = '#b9c3cd';
            let $label_color = '#e7eef7';
            let $purple = '#df87f2';

            // outbound sms
            // -----------------------------

            let smsOutboundOptions = {
                chart: {
                    height: 270,
                    toolbar: {show: false},
                    type: 'line',
                    dropShadow: {
                        enabled: true,
                        top: 20,
                        left: 2,
                        blur: 6,
                        opacity: 0.20
                    },
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                },
                grid: {
                    borderColor: $label_color,
                },
                legend: {
                    show: false,
                },
                colors: [$purple],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        gradientToColors: [$primary],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: $strok_color,
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: {!! $outgoing->xAxis() !!},
                    axisBorder: {
                        show: false,
                    },
                    tickPlacement: 'on',
                    type: 'string'
                },
                yaxis: {
                    tickAmount: 5,
                    labels: {
                        style: {
                            color: $strok_color,
                        },
                        formatter: function (val) {
                            return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
                        }
                    }
                },
                tooltip: {
                    x: {show: false}
                },
                series: {!! $outgoing->dataSet() !!}

            }

            let smsOutbound = new ApexCharts(
                document.querySelector("#sms-outbound"),
                smsOutboundOptions
            );

            smsOutbound.render();


            // inbound sms
            // -----------------------------

            let smsInboundOptions = {
                chart: {
                    height: 270,
                    toolbar: {show: false},
                    type: 'line',
                    dropShadow: {
                        enabled: true,
                        top: 20,
                        left: 2,
                        blur: 6,
                        opacity: 0.20
                    },
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                },
                grid: {
                    borderColor: $label_color,
                },
                legend: {
                    show: false,
                },
                colors: [$purple],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        gradientToColors: [$primary],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: $strok_color,
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: {!! $incoming->xAxis() !!},
                    axisBorder: {
                        show: false,
                    },
                    tickPlacement: 'on',
                    type: 'string'
                },
                yaxis: {
                    tickAmount: 5,
                    labels: {
                        style: {
                            color: $strok_color,
                        },
                        formatter: function (val) {
                            return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
                        }
                    }
                },
                tooltip: {
                    x: {show: false}
                },
                series: {!! $incoming->dataSet() !!}

            }

            let smsInbound = new ApexCharts(
                document.querySelector("#sms-inbound"),
                smsInboundOptions
            );

            smsInbound.render();


            // revenue chart
            // -----------------------------

            let revenueChartOptions = {
                chart: {
                    height: 270,
                    toolbar: {show: false},
                    type: 'line',
                    dropShadow: {
                        enabled: true,
                        top: 20,
                        left: 2,
                        blur: 6,
                        opacity: 0.20
                    },
                },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                },
                grid: {
                    borderColor: $label_color,
                },
                legend: {
                    show: false,
                },
                colors: [$purple],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        inverseColors: false,
                        gradientToColors: [$primary],
                        shadeIntensity: 1,
                        type: 'horizontal',
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100, 100, 100]
                    },
                },
                markers: {
                    size: 0,
                    hover: {
                        size: 5
                    }
                },
                xaxis: {
                    labels: {
                        style: {
                            colors: $strok_color,
                        }
                    },
                    axisTicks: {
                        show: false,
                    },
                    categories: {!! $incoming->xAxis() !!},
                    axisBorder: {
                        show: false,
                    },
                    tickPlacement: 'on',
                    type: 'string'
                },
                yaxis: {
                    tickAmount: 5,
                    labels: {
                        style: {
                            color: $strok_color,
                        },
                        formatter: function (val) {
                            return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
                        }
                    }
                },
                tooltip: {
                    x: {show: false}
                },
                series: {!! $revenue_chart->dataSet() !!}

            }

            let revenueChart = new ApexCharts(
                document.querySelector("#revenue-chart"),
                revenueChartOptions
            );

            revenueChart.render();

        });


        // Client growth Chart
        // ----------------------------------

        let clientGrowthChartoptions = {
            chart: {
                stacked: true,
                type: 'bar',
                toolbar: {show: false},
                height: 290,
            },
            plotOptions: {
                bar: {
                    columnWidth: '70%'
                }
            },
            colors: ['#7367F0'],
            series: {!! $customer_growth->dataSet() !!},
            grid: {
                borderColor: '#e7eef7',
                padding: {
                    left: 0,
                    right: 0
                }
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'left',
                offsetX: 0,
                fontSize: '14px',
                markers: {
                    radius: 50,
                    width: 10,
                    height: 10,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                labels: {
                    style: {
                        colors: '#b9c3cd',
                    }
                },
                axisTicks: {
                    show: false,
                },
                categories: {!! $customer_growth->xAxis() !!},
                axisBorder: {
                    show: false,
                },
            },
            yaxis: {
                tickAmount: 5,
                labels: {
                    style: {
                        color: '#b9c3cd',
                    }
                }
            },
            tooltip: {
                x: {show: false}
            },
        }

        let clientGrowthChart = new ApexCharts(
            document.querySelector("#customer-growth"),
            clientGrowthChartoptions
        );

        clientGrowthChart.render();


        // sms history Chart
        // -----------------------------

        let smsHistoryChartoptions = {
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
            labels: ["{{ __('locale.labels.delivered') }}", "{{ __('locale.labels.failed') }}"],
            series: {!! $sms_history->dataSet() !!},
            dataLabels: {
                enabled: false
            },
            legend: {show: false},
            stroke: {
                width: 5
            },
            colors: ['#7367F0', '#EA5455'],
            fill: {
                type: 'gradient',
                gradient: {
                    gradientToColors: ['#9c8cfc', '#f29292']
                }
            }
        }

        let smsHistoryChart = new ApexCharts(
            document.querySelector("#sms-reports"),
            smsHistoryChartoptions
        );

        smsHistoryChart.render();


    </script>
@endsection
