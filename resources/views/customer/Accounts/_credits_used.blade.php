<div class="row ml-1">
    <div class="col-12">

        {{--sms sending credit--}}
        <div class="">
            <span class="text-uppercase"> {{ __('locale.plans.max_contact_list') }}</span>
            <span class="pull-right text-primary">
                                                       {{ __('locale.labels.using_limits',[
        'used' => \App\Library\Tool::format_number(Auth::user()->customer->listsCount()),
        'remaining' =>  (Auth::user()->customer->maxLists() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxLists())
        ])}}
                                                    </span>
        </div>

        @if(Auth::user()->customer->maxLists() == '∞')
            <div class="progress progress-bar-success progress-xl">
                <div class="progress-bar text-primary progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:0">0%</div>
            </div>
        @else
            <div class="progress progress-bar-success progress-xl">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{ Auth::user()->customer->listsUsage() }}">{{ Auth::user()->customer->listsUsage() }}</div>
            </div>
        @endif
        {{--max contact list end here--}}


        {{--max contact--}}

        <div class="">
            <span class="text-uppercase"> {{ __('locale.plans.max_contact') }}</span>
            <span class="pull-right text-primary">
                                                       {{ __('locale.labels.using_limits',[
        'used' => \App\Library\Tool::format_number(Auth::user()->customer->subscriberCounts()),
        'remaining' =>  (Auth::user()->customer->maxSubscribers() == '∞') ? __('locale.labels.unlimited') : \App\Library\Tool::format_number(Auth::user()->customer->maxSubscribers())
        ])}}
                                                    </span>
        </div>

        @if(Auth::user()->customer->maxSubscribers() == '∞')
            <div class="progress progress-bar-info progress-xl">
                <div class="progress-bar text-primary progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:0">0%</div>
            </div>
        @else
            <div class="progress progress-bar-info progress-xl">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="20" aria-valuemin="20" aria-valuemax="100" style="width:{{ Auth::user()->customer->displaySubscribersUsage() }}">{{ Auth::user()->customer->displaySubscribersUsage() }}</div>
            </div>
        @endif

        {{--max contact end here--}}


    </div>
</div>
