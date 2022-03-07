@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Dashboard'))

@section('vendor-style')
    {{-- Vendor Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection

@section('content')
    {{-- Dashboard Analytics Start --}}
    <section>

        <div class="row">

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0"> {{ \App\Library\Tool::format_number(Auth::user()->customer->listsCount()) }}</h2>
                            <p>{{ __('locale.contacts.contact_groups') }}</p>
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
                            <h2 class="text-bold-700 mb-0">{{ \App\Library\Tool::format_number(Auth::user()->customer->subscriberCounts()) }}</h2>
                            <p>{{ __('locale.menu.Contacts') }}</p>
                        </div>
                        <div class="avatar bg-rgba-success p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-user text-success font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ Auth::user()->customer->blacklistCounts() }}</h2>
                            <p>{{ __('locale.menu.Blacklist') }}</p>
                        </div>
                        <div class="avatar bg-rgba-danger p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-user-x text-danger font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0">
                        <div>
                            <h2 class="text-bold-700 mb-0">{{ Auth::user()->customer->smsTemplateCounts() }}</h2>
                            <p>{{ __('locale.permission.sms_template') }}</p>
                        </div>
                        <div class="avatar bg-rgba-warning p-50 m-0">
                            <div class="avatar-content">
                                <i class="feather icon-inbox text-warning font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-start pb-0"></div>
                    <div class="card-body">

                        <h3 class="text-primary">{{ \App\Helpers\Helper::greetingMessage()}}</h3>
                        <p class="font-medium-2 mt-2">{{ __('locale.description.dashboard', ['brandname' => config('app.name')]) }}</p>

                        <a href="{{ route('customer.view.charts') }}" class="btn btn-primary mt-3"><i class="feather icon-pie-chart"></i> {{ __('locale.menu.View Charts') }}</a>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-primary">{{ __('locale.labels.current_plan')  }}</h3>
                        @if(Auth::user()->customer->activeSubscription() == null)
                            <h3 class="mt-1 text-danger">{{ __('locale.subscription.no_active_subscription') }}</h3>
                        @else
                            <p class="mb-2 mt-1 font-medium-2">{!! __('locale.subscription.you_are_currently_subscribed_to_plan',
                                        [
                                                'plan' => auth()->user()->customer->subscription->plan->name,
                                                'price' => \App\Library\Tool::format_price(auth()->user()->customer->subscription->plan->price, auth()->user()->customer->subscription->plan->currency->format),
                                                'remain' => \App\Library\Tool::formatHumanTime(auth()->user()->customer->subscription->current_period_ends_at),
                                                'end_at' => \App\Library\Tool::customerDateTime(auth()->user()->customer->subscription->current_period_ends_at)
                                        ]) !!}</p>
                        @endif
                        <a href="{{ route('customer.subscriptions.index') }}" class="btn btn-primary mt-3"><i class="feather icon-info"></i> {{ __('locale.labels.more_info') }}</a>
                    </div>
                </div>
            </div>

        </div>


        <div class="row">

            <div class="col-lg-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="card-title text-uppercase">{{ __('locale.labels.sms_reports') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div id="sms-reports"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="mb-0">{{ __('locale.contacts.contact_groups') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body px-0 pb-0">
                            <div id="max-contact-list-chart" class="mt-75"></div>
                            <div class="row text-center mx-0">
                                <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                                    <p class="mb-50">{{ __('locale.labels.total') }}</p>
                                    <p class="font-large-1 text-bold-700 mb-50">{{ (Auth::user()->customer->maxLists() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxLists())}}</p>
                                </div>
                                <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                                    <p class="mb-50">{{ __('locale.labels.remaining') }}</p>
                                    <p class="font-large-1 text-bold-700 mb-50">
                                    {{ (Auth::user()->customer->maxLists() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxLists() - Auth::user()->customer->listsCount())}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-end">
                        <h4 class="mb-0">{{ __('locale.plans.max_contact') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body px-0 pb-0">
                            <div id="max-contacts-chart" class="mt-75"></div>
                            <div class="row text-center mx-0">
                                <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                                    <p class="mb-50">{{ __('locale.labels.total') }}</p>
                                    <p class="font-large-1 text-bold-700 mb-50">
                                        {{ (Auth::user()->customer->maxSubscribers() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxSubscribers()) }}
                                    </p>
                                </div>
                                <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                                    <p class="mb-50">{{ __('locale.labels.remaining') }}</p>
                                    <p class="font-large-1 text-bold-700 mb-50">
                                        {{ (Auth::user()->customer->maxSubscribers() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxSubscribers() - Auth::user()->customer->subscriberCounts()) }}

                                    </p>
                                </div>
                            </div>
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
    <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('page-script')

    @if(Auth::user()->customer->activeSubscription() == null)

        <script>

            let CustomerSendingQuota = 0
            let CustomerMaxLists = 0;
            let CustomerMaxContacts = 0;

            $(window).on("load", function () {

                let $primary = '#7367F0';
                let $success = '#00db89';
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


                // sms sending credit  Chart
                // -----------------------------

                let smsCreditChartoptions = {
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
                    series: [parseFloat(CustomerSendingQuota).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let smsCreditChart = new ApexCharts(
                    document.querySelector("#sms-sending-credit-chart"),
                    smsCreditChartoptions
                );

                smsCreditChart.render();

                // contact list  Chart
                // -----------------------------

                let contactListChartoptions = {
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
                    series: [parseFloat(CustomerMaxLists).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let contactListChart = new ApexCharts(
                    document.querySelector("#max-contact-list-chart"),
                    contactListChartoptions
                );

                contactListChart.render();


                // contact  Chart
                // -----------------------------

                let contactChartoptions = {
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
                    series: [parseFloat(CustomerMaxContacts).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let contactChart = new ApexCharts(
                    document.querySelector("#max-contacts-chart"),
                    contactChartoptions
                );

                contactChart.render();


            });

        </script>
    @else

        <script>

            let CustomerSendingQuota = "{{ Auth::user()->customer->getSendingQuota() }}";

            if (CustomerSendingQuota === '-1') {
                CustomerSendingQuota = '0'
            } else {
                CustomerSendingQuota = "{{ Auth::user()->customer->getSendingQuotaUsage() != 0 ? Auth::user()->customer->getSendingQuotaUsage() / Auth::user()->customer->getSendingQuota() *100 : 0 }}"
            }

            let CustomerMaxLists = "{{ Auth::user()->customer->getOption('list_max') }}";

            if (CustomerMaxLists === '-1') {
                CustomerMaxLists = '0'
            } else {
                CustomerMaxLists = "{{ Auth::user()->customer->listsCount() != 0 ? Auth::user()->customer->listsCount() / Auth::user()->customer->getOption('list_max') *100 : 0 }}"
            }


            let CustomerMaxContacts = "{{ Auth::user()->customer->getOption('subscriber_max') }}";

            if (CustomerMaxContacts === '-1') {
                CustomerMaxContacts = '0'
            } else {
                CustomerMaxContacts = "{{ Auth::user()->customer->subscriberCounts() !=0 ? Auth::user()->customer->subscriberCounts() / Auth::user()->customer->getOption('subscriber_max') *100 : 0 }}"
            }

            $(window).on("load", function () {

                let $primary = '#7367F0';
                let $success = '#00db89';
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


                // sms sending credit  Chart
                // -----------------------------

                let smsCreditChartoptions = {
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
                    series: [parseFloat(CustomerSendingQuota).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let smsCreditChart = new ApexCharts(
                    document.querySelector("#sms-sending-credit-chart"),
                    smsCreditChartoptions
                );

                smsCreditChart.render();

                // contact list  Chart
                // -----------------------------

                let contactListChartoptions = {
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
                    series: [parseFloat(CustomerMaxLists).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let contactListChart = new ApexCharts(
                    document.querySelector("#max-contact-list-chart"),
                    contactListChartoptions
                );

                contactListChart.render();


                // contact  Chart
                // -----------------------------

                let contactChartoptions = {
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
                    series: [parseFloat(CustomerMaxContacts).toFixed(1)],
                    stroke: {
                        lineCap: 'round'
                    },

                }

                let contactChart = new ApexCharts(
                    document.querySelector("#max-contacts-chart"),
                    contactChartoptions
                );

                contactChart.render();


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

            });

        </script>

    @endif

@endsection
