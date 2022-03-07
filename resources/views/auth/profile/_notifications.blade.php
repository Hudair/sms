<div id="data-list-view" class="data-list-view-header">
    <div class="action-btns d-none">
        <div class="btn-dropdown mr-1 mb-1 add-new-div">

            <div class="btn-group dropdown actions-dropodown">
                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ __('locale.labels.actions') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item bulk-read" href="#"><i class="feather icon-check-circle"></i>Mark as read</a>
                    <a class="dropdown-item bulk-delete" href="#"><i class="feather icon-trash"></i>{{ __('locale.datatables.bulk_delete') }}</a>
                </div>
            </div>


        </div>

    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        <table class="table data-list-view">
            <thead>
            <tr>
                <th></th>
                <th>{{__('locale.labels.type')}} </th>
                <th>{{__('locale.labels.message')}} </th>
                <th>{{__('locale.labels.status')}}</th>
                <th>{{__('locale.labels.actions')}}</th>
            </tr>
            </thead>
        </table>
    </div>
    {{-- DataTable ends --}}

</div>
