<div class="mb-2 mt-2">
    <div class="btn-group">
        <button
                class="btn btn-primary fw-bold dropdown-toggle"
                type="button"
                id="bulk_actions"
                data-bs-toggle="dropdown"
                aria-expanded="false"
        >
            {{ __('locale.labels.actions') }}
        </button>
        <div class="dropdown-menu" aria-labelledby="bulk_actions">
            <a class="dropdown-item bulk-read" href="#"><i data-feather="stop-circle"></i> Mark as read</a>
            <a class="dropdown-item bulk-delete" href="#"><i data-feather="trash"></i> {{ __('locale.datatables.bulk_delete') }}</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="table datatables-basic">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ __('locale.labels.id') }}</th>
                        <th>{{__('locale.labels.type')}} </th>
                        <th>{{__('locale.labels.message')}} </th>
                        <th>Mark as Read</th>
                        <th>{{__('locale.labels.actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
