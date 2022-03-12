<!-- current plan -->
<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">{{ __('locale.labels.current_plan') }}</h4>
    </div>
    <div class="card-body my-2 py-25">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-2 pb-50">
                    <h5>{!!  __('locale.subscription.current_plan_information', ['plan_name' => $plan->name]) !!}</h5>
                    <span>{{ $plan->description }}</span>
                </div>
                <div class="mb-2 pb-50">
                    <h5>{!! __('locale.subscription.active_until', ['date' => \App\Library\Tool::formatDateTime($subscription->current_period_ends_at)]) !!}</h5>
                    <span>{{ __('locale.subscription.current_plan_notification') }}</span>
                </div>
                <div class="mb-1">
                    <h5>{{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }} {{ __('locale.labels.per') }} {{ $plan->displayFrequencyTime() }}
                        @if($plan->is_popular)
                            <span class="badge badge-light-primary ms-50">{{ __('locale.labels.popular') }}</span>
                        @endif
                    </h5>
                    <span>{{ $plan->description }}</span>
                </div>
            </div>
            <div class="col-12">

                <a href="{{ route('customer.subscriptions.renew', $subscription->uid) }}" class="btn btn-primary me-1 mt-1"><i data-feather="repeat"></i> {{ __('locale.labels.renew') }}</a>
                <a href="{{ route('customer.subscriptions.change_plan', $subscription->uid) }}" class="btn btn-success me-1 mt-1"><i data-feather="refresh-cw"></i> {{ __('locale.labels.change_plan') }}</a>
                <span class="btn btn-danger mt-1 action-cancel" data-id={{ $subscription->uid }}><i data-feather="stop-circle"></i> {{ __('locale.buttons.cancel') }}</span>
            </div>
        </div>
    </div>
</div>
<!-- / current plan -->


<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">{{ __('locale.labels.plan_details') }}</h4>
    </div>
    <div class="table-responsive">

        <table class="table">
            <tbody>
            <tr>
                <td> {{ __('locale.labels.plan_name') }} </td>
                <td class="text-primary font-medium-2"> {{$plan->name}} </td>
            </tr>

            <tr>
                <td> {{ __('locale.plans.price') }} </td>
                <td> {{ \App\Library\Tool::format_price($plan->price, $plan->currency->format) }} </td>
            </tr>

            <tr>
                <td> {{ __('locale.labels.renew') }} </td>
                <td> {{ __('locale.labels.every') }} {{ $plan->displayFrequencyTime() }} </td>
            </tr>

            <tr>
                <td> {{ __('locale.labels.sms_credit') }} </td>
                <td> {{ $plan->displayTotalQuota() }} </td>
            </tr>

            <tr>
                <td> {{ __('locale.plans.create_own_sending_server') }} </td>
                <td>
                    @if($plan->getOption('create_sending_server') == 'yes')
                        {{__('locale.labels.yes')}}
                    @else
                        {{__('locale.labels.no')}}
                    @endif
                </td>
            </tr>

            <tr>
                <td> {{ __('locale.customer.sender_id_verification') }} </td>
                <td>
                    @if($plan->getOption('sender_id_verification') == 'yes')
                        {{__('locale.labels.yes')}}
                    @else
                        {{__('locale.labels.no')}}
                    @endif
                </td>
            </tr>

            <tr>
                <td> {{ __('locale.labels.cutting_system_available') }} </td>
                <td>
                    {{__('locale.labels.yes')}}
                </td>
            </tr>


            <tr>
                <td> {{ __('locale.labels.api_access') }} </td>
                <td>
                    @if($plan->getOption('api_access') == 'yes')
                        {{__('locale.labels.yes')}}
                    @else
                        {{__('locale.labels.no')}}
                    @endif
                </td>
            </tr>

            <tr>
                <td>{{ __('locale.plans.max_contact_list') }}</td>
                <td>{{ $plan->displayMaxList() }}</td>
            </tr>

            <tr>
                <td>{{ __('locale.plans.max_contact') }}</td>
                <td>{{ $plan->displayMaxContact() }}</td>
            </tr>

            <tr>
                <td>{{ __('locale.plans.max_contact_per_list') }}</td>
                <td>{{ $plan->displayMaxContactPerList() }}</td>
            </tr>

            <tr>
                <td>{{ __('locale.labels.text_messages') }}</td>
                <td>{{ $plan->getOption('plain_sms') }} {{__('locale.labels.credit_per_sms')}}</td>
            </tr>

            <tr>
                <td>{{ __('locale.labels.voice_messages') }}</td>
                <td>{{ $plan->getOption('voice_sms') }} {{__('locale.labels.credit_per_sms')}}</td>
            </tr>

            <tr>
                <td>{{ __('locale.labels.picture_messages') }}</td>
                <td>{{ $plan->getOption('mms_sms') }} {{__('locale.labels.credit_per_sms')}}</td>
            </tr>


            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom">
        <h4 class="card-title">{{ __('locale.labels.transactions') }}</h4>
    </div>
    <div class="card-body my-2 py-25">
        <div class="row">
            <p>{!! __('locale.description.subscription_logs') !!}</p>
            <div class="col-12">
                <ul class="nav nav-pills mb-2 mt-3 text-uppercase" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#logs" aria-controls="logs" role="tab" aria-selected="true">
                            <i data-feather="database" class="mr-25"></i>{{__('locale.subscription.logs')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="information-tab" data-bs-toggle="tab" href="#transactions" aria-controls="transactions" role="tab" aria-selected="false">
                            <i data-feather="shopping-cart"></i>{{__('locale.labels.transactions')}}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="logs" aria-labelledby="account-tab" role="tabpanel">
                        <div class="row table-responsive">
                            <table class="table">
                                <thead class="thead-primary">
                                <tr>
                                    <th scope="col">{{ __('locale.labels.created_at') }}</th>
                                    <th scope="col">{{ __('locale.labels.message') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($subscription->getLogs() as $key => $log)
                                    <tr>
                                        <td> {{ \App\Library\Tool::customerDateTime($log->created_at) }} </td>
                                        <td> {!! __('locale.subscription.log_' . $log->type, $log->getData()) !!} </td>
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

                    <div class="tab-pane" id="transactions" aria-labelledby="information-tab" role="tabpanel">
                        <div class="row table-responsive">
                            <table class="table">
                                <thead class="thead-primary">
                                <tr>
                                    <th scope="col">{{ __('locale.labels.created_at') }}</th>
                                    <th scope="col">{{ __('locale.labels.title') }}</th>
                                    <th scope="col">{{ __('locale.labels.amount') }}</th>
                                    <th scope="col">{{ __('locale.labels.status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($subscription->getTransactions() as $key => $invoice)
                                    <tr>
                                        <td>{{ \App\Library\Tool::customerDateTime($invoice->created_at) }}</td>
                                        <td>
                                            {!! $invoice->title !!}
                                            @if ($invoice->description)
                                                <div class="small text-muted">{!! $invoice->description !!}</div>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->amount }}</td>
                                        <td>
                                            @if($invoice->status == 'failed')
                                                <span class="badge text-uppercase bg-danger">
                                                    @elseif($invoice->status == 'pending' || $invoice->status == 'plan_change' || $invoice->status == 'renew' )
                                                        <span class="badge text-uppercase bg-warning">
                                                            @elseif($invoice->status == 'auto_charge')
                                                                <span class="badge text-uppercase bg-info">
                                                                    @else
                                                                        <span class="badge text-uppercase bg-success">
                                                                            @endif
                                                                            {{ str_replace('_', ' ', $invoice->status) }}
                                                                        </span>
                                                                </span>
                                                        </span>
                                                </span>
                                        </td>
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

                </div>
            </div>
        </div>

    </div>
</div>
