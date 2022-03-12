<button type="button" class="btn btn-sm btn-warning mb-75 me-75" data-bs-toggle="modal" data-bs-target="#removeUnit"><i data-feather="minus-square"></i> {{__('locale.labels.remove_unit')}}</button>

{{-- Modal --}}
<div class="modal fade text-left" id="removeUnit" tabindex="-1" role="dialog" aria-labelledby="removeUnitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="removeUnitLabel">{{__('locale.labels.remove_unit')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.customers.remove_unit', $customer->uid) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>{!! __('locale.description.add_unit') !!}</p>

                    <div class="form-group">
                        <label for="remove_unit" class="required">{{__('locale.labels.remove_unit')}}</label>
                        <input type="number" id="remove_unit" class="form-control @error('add_unit') is-invalid @enderror" value="{{ old('add_unit') }}" name="add_unit" required>
                        @error('add_unit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i data-feather="minus-square"></i> {{__('locale.labels.remove_unit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
