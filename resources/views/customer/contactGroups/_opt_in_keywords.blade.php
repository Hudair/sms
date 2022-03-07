<div id="opt-in-keywords" class="data-list-view-header">

    <div class="action-btns d-none">
        <div class="btn-dropdown mr-1 mb-1 add-new-keyword">
                <div class="btn-group dropdown actions-dropodown">
                    <a href="#" class="btn btn-white px-1 py-1 waves-effect waves-light text-success text-bold-500 add_opt_in_keyword"> {{__('locale.buttons.add_new')}}
                        <i class="feather icon-plus-circle"></i>
                    </a>
                </div>
        </div>
    </div>

    {{-- DataTable starts --}}
    <div class="table-responsive">
        <table class="table opt-in-keywords">
            <thead>
            <tr>
                <th>{{__('locale.labels.keyword')}} </th>
                <th>{{__('locale.labels.added_at')}}</th>
                <th>{{__('locale.labels.actions')}}</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($opt_in_keywords as $keywords)
                <tr>
                    <td>{{ $keywords->keyword }}</td>
                    <td>{{ \App\Library\Tool::formatHumanTime($keywords->created_at) }}</td>
                    <td>
                        <span class='action-delete-optin-keyword text-danger' data-id='{{$keywords->uid}}' data-toggle='tooltip' data-placement='top' title='{{__('locale.buttons.delete')}}'><i class='feather us-2x icon-trash'></i></span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{-- DataTable ends --}}
</div>
