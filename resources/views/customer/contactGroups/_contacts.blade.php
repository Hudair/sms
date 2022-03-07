<div id="data-list-view" class="data-list-view-header contacts">
    <div class="action-btns d-none">
        <div class="btn-dropdown mr-1 mb-1 add-new-div">

            @can('view_contact')
                <div class="btn-group dropdown actions-dropodown">
                    <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('locale.labels.actions') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item bulk-subscribe" href="#"><i
                                    class="feather icon-check"></i>{{ __('locale.labels.subscribe') }}</a>
                        <a class="dropdown-item bulk-unsubscribe" href="#"><i
                                    class="feather icon-stop-circle"></i>{{ __('locale.labels.unsubscribe') }}</a>
                        <a class="dropdown-item bulk-copy" href="#"><i
                                    class="feather icon-copy"></i>{{ __('locale.buttons.copy') }}</a>
                        <a class="dropdown-item bulk-move" href="#"><i
                                    class="feather icon-move"></i>{{ __('locale.buttons.move') }}</a>
                        <a class="dropdown-item bulk-delete" href="#"><i
                                    class="feather icon-trash"></i>{{ __('locale.datatables.bulk_delete') }}</a>
                    </div>
                </div>
            @endcan

            @can('create_contact')
                <div class="btn-group dropdown actions-dropodown">
                    <a href="{{ route('customer.contact.create', $contact->uid) }}"
                       class="btn btn-white px-1 py-1 waves-effect waves-light text-success text-bold-500"> {{__('locale.buttons.add_new')}}
                        <i class="feather icon-plus-circle"></i></a>
                </div>
            @endcan

            @can('view_contact')
                <div class="btn-group dropdown actions-dropodown">
                    <a href="{{ route('customer.contact.import', $contact->uid) }}"
                       class="btn btn-white px-1 py-1 waves-effect waves-light text-primary text-bold-500"> {{__('locale.buttons.import')}}
                        <i class="feather icon-download"></i></a>
                </div>

                <div class="btn-group dropdown actions-dropodown">
                    <a href="{{ route('customer.contact.export', $contact->uid) }}"
                       class="btn btn-white px-1 py-1 waves-effect waves-light text-info text-bold-500"> {{__('locale.buttons.export')}}
                        <i class="feather icon-upload"></i></a>
                </div>

            @endcan

        </div>

    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        <table class="table data-list-view">
            <thead>
            <tr>
                <th></th>
                <th>{{__('locale.menu.Contacts')}}</th>
                <th>{{__('locale.labels.name')}} </th>
                <th>{{__('locale.labels.created_at')}}</th>
                <th>{{__('locale.labels.status')}}</th>
                <th>{{__('locale.labels.actions')}}</th>
            </tr>
            </thead>
        </table>
    </div>
    {{-- DataTable ends --}}
</div>
