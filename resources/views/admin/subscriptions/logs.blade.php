@extends('layouts/contentLayoutMaster')

@section('title', __('locale.subscription.logs'))

@section('content')
    <!-- users edit start -->
    <section class="users-edit">
        <div class="card">
            <div class="card-content">

                <div class="card-body">

                    <p>{!! __('locale.description.subscription_logs') !!}</p>

                    <ul class="nav nav-tabs nav-justified mb-3" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link active" id="account-tab" data-bs-toggle="tab" href="#logs" aria-controls="logs" role="tab" aria-selected="true">
                                <i data-feather="database"></i>{{__('locale.subscription.logs')}}
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
                                    @forelse ($subscription->getTransactions() as $key => $invoice)
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
                                                    <span class="badge text-uppercase badge-light-danger">
                                                @elseif($invoice->status == 'pending' || $invoice->status == 'plan_change' || $invoice->status == 'renew' )
                                                     <span class="badge text-uppercase badge-light-warning">
                                                @elseif($invoice->status == 'auto_charge')
                                                     <span class="badge text-uppercase badge-light-info">
                                                @else
                                                      <span class="badge text-uppercase badge-light-success">
                                                @endif
                                                          {{ str_replace('_', ' ', $invoice->status) }}
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
    </section>
    <!-- users edit ends -->
@endsection

