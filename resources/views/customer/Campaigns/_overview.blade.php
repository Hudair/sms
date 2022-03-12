<div class="row">
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="fw-bolder mb-0">{{ $campaign->readCache('ContactCount') }}</h2>
                    <p class="card-text">{{ __('locale.labels.recipients') }}</p>
                </div>
                <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content">
                        <i data-feather="users" class="font-medium-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="fw-bolder mb-0">{{ $campaign->readCache('DeliveredCount') }}</h2>
                    <p class="card-text">{{ __('locale.labels.delivered') }}</p>
                </div>
                <div class="avatar bg-light-success p-50 m-0">
                    <div class="avatar-content">
                        <i data-feather="check-square" class="font-medium-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="fw-bolder mb-0">{{ $campaign->readCache('FailedDeliveredCount') }}</h2>
                    <p class="card-text">{{ __('locale.labels.failed') }}</p>
                </div>
                <div class="avatar bg-light-danger p-50 m-0">
                    <div class="avatar-content">
                        <i data-feather="x-square" class="font-medium-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-4 col-md-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-end">
                <h4 class="mb-0 text-uppercase text-primary">{{__('locale.menu.Overview')}}</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <h5 class="mb-1">{{ __('locale.labels.campaign_name') }}: <span class="fw-bold"> {{ $campaign->campaign_name }}</span></h5>
                    <h5 class="mb-1">{{ __('locale.labels.campaign_id') }}: <span class="fw-bold"> {{ $campaign->uid }}</span></h5>
                    <h5>{{ __('locale.labels.campaigns_type') }}: <span class="fw-bold"> {!! $campaign->getCampaignType() !!}</span></h5>
                    <h5 class="mb-1">{{ __('locale.labels.status') }}: <span class="fw-bold"> {{ $campaign->status }}</span></h5>


                    @if($campaign->status == \App\Models\Campaigns::STATUS_FAILED || $campaign->status == \App\Models\Campaigns::STATUS_CANCELLED)
                        <h5 class="mb-1">{{ __('locale.labels.reason') }}: <code> {{ $campaign->reason }}</code></h5>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-4 col-md-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-end">
                <h4 class="card-title">{{ __('locale.labels.success_rate') }}</h4>
            </div>

            <div class="card-body p-0">
                <div id="goal-overview-chart" class="my-2"></div>

                <div class="row border-top text-center mx-0">
                    <div class="col-6 border-end py-1">
                        <p class="card-text text-muted mb-0">{{ __('locale.labels.success') }}</p>
                        <h3 class="fw-bolder mb-0">{{ $campaign->readCache('DeliveredCount') }}</h3>
                    </div>
                    <div class="col-6 py-1">
                        <p class="card-text text-muted mb-0">{{ __('locale.labels.failed') }}</p>
                        <h3 class="fw-bolder mb-0">{{ $campaign->readCache('FailedDeliveredCount') }}</h3>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-end">
                <h4 class="card-title text-uppercase">{{ __('locale.labels.sms_reports') }}</h4>
            </div>
            <div class="card-content">
                <div class="card-body p-0">
                    <div id="sms-reports" class="my-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

