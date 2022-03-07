<div class="row">
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card bg-gradient-primary">
            <div class="card-header d-flex align-items-start pb-0">
                <div>
                    <h2 class="text-bold-700 text-white mb-0">{{ $campaign->readCache('ContactCount') }}</h2>
                    <p>{{ __('locale.labels.recipients') }}</p>
                </div>
                <div class="avatar bg-rgba-white p-50 m-0">
                    <div class="avatar-content">
                        <i class="feather icon-users text-white font-medium-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card bg-gradient-success">
            <div class="card-header d-flex align-items-start pb-0">
                <div>
                    <h2 class="text-bold-700 text-white mb-0">{{ $campaign->readCache('DeliveredCount') }}</h2>
                    <p>{{ __('locale.labels.delivered') }}</p>
                </div>
                <div class="avatar bg-rgba-white p-50 m-0">
                    <div class="avatar-content">
                        <i class="feather icon-check-square text-white font-medium-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-12">
        <div class="card bg-gradient-danger">
            <div class="card-header d-flex align-items-start pb-0">
                <div>
                    <h2 class="text-bold-700 text-white mb-0">{{ $campaign->readCache('FailedDeliveredCount') }}</h2>
                    <p>{{ __('locale.labels.failed') }}</p>
                </div>
                <div class="avatar bg-rgba-white p-50 m-0">
                    <div class="avatar-content">
                        <i class="feather icon-x-square text-white font-medium-5"></i>
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
                    <h5 class="mb-1">{{ __('locale.labels.campaign_name') }}: <span class="text-bold-400"> {{ $campaign->campaign_name }}</span></h5>
                    <h5 class="mb-1">{{ __('locale.labels.campaign_id') }}: <span class="text-bold-400"> {{ $campaign->uid }}</span></h5>
                    <h5>{{ __('locale.labels.campaigns_type') }}: <span class="text-bold-400"> {!! $campaign->getCampaignType() !!}</span></h5>
                    <h5 class="mb-1">{{ __('locale.labels.status') }}: <span class="text-bold-400"> {{ $campaign->status }}</span></h5>


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
                <h4 class="mb-0 text-uppercase text-primary">Success Rate</h4>
            </div>
            <div class="card-content">
                <div class="card-body px-0 pb-0">
                    <div id="goal-overview-chart" class="mt-75"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-end">
            </div>
            <div class="card-content">
                <div class="card-body py-0">
                    <div id="customer-chart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
