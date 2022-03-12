<div id="datatables-basic">

    <div class="mb-3 mt-2">
        @if(Auth::user()->customer->getOption('delete_sms_history') == 'yes')
            <div class="btn-group">
                <button
                        class="btn btn-primary fw-bold dropdown-toggle me-1"
                        type="button"
                        id="bulk_actions"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    {{ __('locale.labels.actions') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="bulk_actions">
                    <a class="dropdown-item bulk-delete" href="#"><i data-feather="trash"></i> {{ __('locale.datatables.bulk_delete') }}</a>
                </div>
            </div>
        @endif


        @if(Auth::user()->customer->getOption('list_export') == 'yes')
            <div class="btn-group">
                <a href="{{ route('customer.reports.export.campaign', $campaign->uid) }}" class="btn btn-info waves-light waves-effect fw-bold"> {{__('locale.buttons.export')}} <i data-feather="file-text"></i></a>
            </div>
        @endif

    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <table class="table datatables-basic">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{__('locale.labels.date')}}</th>
                        <th>{{__('locale.labels.from')}}</th>
                        <th>{{__('locale.labels.to')}}</th>
                        <th>{{__('locale.labels.cost')}}</th>
                        <th>{{__('locale.labels.status')}}</th>
                        <th>{{__('locale.labels.actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>
