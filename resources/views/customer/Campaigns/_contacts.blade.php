<div id="data-list-view" class="data-list-view-header">

    <div class="action-btns d-none">
        <div class="btn-dropdown mr-1 mb-1 add-new-div">
            @if(Auth::user()->customer->getOption('delete_sms_history') == 'yes')
                <div class="btn-group dropdown actions-dropodown">
                    <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('locale.labels.actions') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item bulk-delete" href="#"><i class="feather icon-trash"></i>{{ __('locale.datatables.bulk_delete') }}</a>
                    </div>
                </div>
            @endif

            @if(Auth::user()->customer->getOption('list_export') == 'yes')
                <div class="btn-group dropdown actions-dropodown">
                    <a href="{{ route('customer.reports.export.campaign', $campaign->uid) }}" class="btn btn-white px-1 py-1 waves-effect waves-light text-info text-bold-500"> {{__('locale.buttons.export')}} <i class="feather icon-file-text"></i></a>
                </div>
            @endif

        </div>

    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        <table class="table data-list-view">
            <thead>
            <tr>
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
    {{-- DataTable ends --}}

</div>
