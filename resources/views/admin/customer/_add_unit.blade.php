<button type="button" class="btn btn-outline-success d-none d-sm-block mr-75" data-toggle="modal" data-target="#addUnit"><i class="feather icon-plus-square"></i> {{__('locale.labels.add_unit')}}</button>

{{-- Modal --}}
<div class="modal fade text-left" id="addUnit" tabindex="-1" role="dialog" aria-labelledby="addUnitLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addUnitLabel">{{__('locale.labels.add_unit')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                    <button type="submit" class="btn btn-primary">{{__('locale.buttons.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
