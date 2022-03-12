<div class="card">
    <div class="card-body py-2 my-25">
        <p class="ml-2">{!! __('locale.description.cutting_system') !!} </p>
        <div class="col-md-6 col-12">
            <div class="form-body">

                <form class="form form-vertical" action="{{ route('admin.plans.settings.cutting-system', $plan->uid) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required" for="cutting_value">{{__('locale.sending_servers.cutting_value')}}</label>
                                <input type="number" id="cutting_value" class="form-control @error('cutting_value') is-invalid @enderror" value="{{ $options['cutting_value'] }}" name="cutting_value" required>
                                @error('cutting_value')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required" for="cutting_unit">{{__('locale.sending_servers.cutting_unit')}}</label>
                                <select class="form-select" id="cutting_unit" name="cutting_unit">
                                    <option value="percentage" {{ $options['cutting_unit'] == 'percentage' ? 'selected': null }}>{{__('locale.labels.percentage')}}</option>
                                    <option value="digit" {{ $options['cutting_unit'] == 'digit' ? 'selected': null }}>{{__('locale.labels.digit')}}</option>
                                </select>
                                @error('cutting_unit')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required" for="cutting_logic">{{__('locale.sending_servers.cutting_logic')}}</label>
                                <select class="form-select" id="cutting_logic" name="cutting_logic">
                                    <option value="random" {{ $options['cutting_logic'] == 'random' ? 'selected': null }}> {{__('locale.sending_servers.cutting_random')}}</option>
                                    <option value="start" {{ $options['cutting_logic'] == 'start' ? 'selected': null }}>  {{__('locale.sending_servers.cutting_from_start')}}</option>
                                    <option value="end" {{ $options['cutting_logic'] == 'end' ? 'selected': null }}>  {{__('locale.sending_servers.cutting_from_end')}}</option>
                                </select>
                                @error('cutting_unit')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary mb-1">
                                <i data-feather="save"></i> {{__('locale.buttons.save')}}
                            </button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
