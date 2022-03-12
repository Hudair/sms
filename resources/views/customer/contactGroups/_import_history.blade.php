<div class="row">
    <div class="col-12">
        <div class="card">
            <table class="table mb-0">
                <thead class="thead-primary">
                <tr>
                    <th scope="col">{{ __('locale.labels.submitted') }}</th>
                    <th scope="col">{{ __('locale.labels.status') }}</th>
                    <th scope="col">{{ __('locale.labels.message') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($import_history as $history)
                    <tr>
                        <td> {{ \App\Library\Tool::customerDateTime($history->created_at) }} </td>
                        <td> {!! $history->getStatus() !!} </td>
                        <td>{{ $history->getOption('message') }}</td>
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
