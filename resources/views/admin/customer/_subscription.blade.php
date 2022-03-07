<div class="row">

    @if(isset($customer->customer->subscription))

        <div class="col-12">
            <p class="mb-2">{!! __('locale.subscription.you_are_currently_subscribed_to_plan',
                                        [
                                                'plan' => $customer->customer->currentPlanName(),
                                                'price' => \App\Library\Tool::format_price($customer->customer->activeSubscription()->plan->price, $customer->customer->activeSubscription()->plan->currency->format),
                                                'remain' => \App\Library\Tool::formatHumanTime($customer->customer->subscription->current_period_ends_at),
                                                'end_at' => \App\Library\Tool::formatDate($customer->customer->subscription->current_period_ends_at)
                                        ]) !!}</p>

        </div>

        <div class="col-12">
            <p>{!! __('locale.description.subscription_logs') !!}</p>

            <ul class="nav nav-tabs nav-justified mb-3" role="tablist">

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
                            @forelse ($customer->customer->subscription->getLogs() as $key => $log)
                                <tr>
                                    <td> {{ \App\Library\Tool::formatDateTime($log->created_at) }} </td>
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
                            @forelse ($customer->customer->subscription->getTransactions() as $key => $invoice)
                                <tr>
                                    <td>{{ \App\Library\Tool::formatDate($invoice->created_at) }}</td>
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
    @else
        <div class="col-12">
            <h5 class="text-center text-info">{!! __('locale.subscription.no_active_subscription')  !!}</h5>
            <div class="row justify-content-center mt-2">
                <a href="{{ route('admin.subscriptions.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">{{ __('locale.buttons.new_subscription') }}</a>
            </div>
        </div>
    @endif


</div>
