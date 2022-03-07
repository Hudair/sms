<div class="row ml-1">
    <div class="col-12">
        <p class="mb-2">{!! __('locale.subscription.you_are_currently_subscribed_to_plan',
                                        [
                                                'plan' => $plan->name,
                                                'price' => \App\Library\Tool::format_price($plan->price, $plan->currency->format),
                                                'remain' => \App\Library\Tool::formatHumanTime($subscription->current_period_ends_at),
                                                'end_at' => \App\Library\Tool::customerDateTime($subscription->current_period_ends_at)
                                        ]) !!}</p>

        <a href="{{ route('customer.subscriptions.renew', $subscription->uid) }}" class="btn btn-primary square mr-1 mb-1"><i class="feather icon-repeat"></i> {{ __('locale.labels.renew') }}</a>
        <a href="{{ route('customer.subscriptions.change_plan', $subscription->uid) }}" class="btn btn-success square mr-1 mb-1"><i class="feather icon-refresh-cw"></i> {{ __('locale.labels.change_plan') }}</a>
        <span class="btn btn-danger square mr-1 mb-1 action-cancel" data-id={{ $subscription->uid }}><i class="feather icon-stop-circle"></i> {{ __('locale.buttons.cancel') }}</span>
    </div>


    <div class="col-12 mt-5">

        <h4 class="text-bold-600 font-medium-4 text-primary mb-1">
            {{__('locale.labels.plan_details')}}
        </h4>

        <p>{!! __('locale.description.current_plan_details') !!}</p>


        <div class="table-responsive">
            <table class="table mb-0">
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


    <div class="col-12 mt-5">

        <h4 class="text-bold-600 font-medium-4 text-primary mb-1">
            {{__('locale.subscription.logs')}} & {{ __('locale.labels.transactions') }}
        </h4>

        <p>{!! __('locale.description.subscription_logs') !!}</p>

        <div class="nav-vertical">
            <ul class="nav nav-tabs nav-left flex-column mb-3" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" id="account-tab" data-toggle="tab" href="#logs" aria-controls="logs" role="tab" aria-selected="true">
                        <i class="feather icon-database mr-25"></i>{{__('locale.subscription.logs')}}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="information-tab" data-toggle="tab" href="#transactions" aria-controls="transactions" role="tab" aria-selected="false">
                        <i class="feather icon-shopping-cart mr-25"></i>{{__('locale.labels.transactions')}}
                    </a>
                </li>
            </ul>


            <div class="tab-content">

                <div class="tab-pane active" id="logs" aria-labelledby="account-tab" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table mb-0">
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
                    <div class="table-responsive">
                        <table class="table mb-0">
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
                                            <div class="chip chip-danger">
                                                @elseif($invoice->status == 'pending' || $invoice->status == 'plan_change' || $invoice->status == 'renew' )
                                                    <div class="chip chip-warning">
                                                        @elseif($invoice->status == 'auto_charge')
                                                            <div class="chip chip-info">
                                                                @else
                                                                    <div class="chip chip-success">
                                                                        @endif
                                                                        <div class="chip-body">
                                                                            <div class="chip-text text-uppercase"> {{ str_replace('_', ' ', $invoice->status) }}</div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>
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
