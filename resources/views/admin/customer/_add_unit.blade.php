<button type="button" class="btn btn-sm btn-success mb-75 me-75" data-bs-toggle="modal" data-bs-target="#addUnit"><i data-feather="plus-square"></i> {{__('locale.labels.add_unit')}}</button>

{{-- Modal --}}
<div class="modal fade text-left" id="addUnit" tabindex="-1" role="dialog" aria-labelledby="addUnitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addUnitLabel">{{__('locale.labels.add_unit')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.customers.add_unit', $customer->uid) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>{!! __('locale.description.add_unit') !!}</p>

                    <div class="form-group">
                        <label for="add_unit" class="required">{{__('locale.labels.add_unit')}}</label>
                        <input type="number" id="add_unit" class="form-control @error('add_unit') is-invalid @enderror" value="{{ old('add_unit') }}" name="add_unit" required>
                        @error('add_unit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i data-feather="plus-square"></i> {{__('locale.labels.add_unit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
