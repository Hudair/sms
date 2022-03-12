<div class="card">
    <div class="card-body py-2 my-25">
        <div class="col-md-6 col-12">
            <div class="form-body">
                <form class="form form-vertical" action="{{ route('admin.plans.settings.speed-limit', $plan->uid) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="sending_limit" class="form-label required">{{__('locale.plans.speed_limit')}}</label>
                                <select class="form-select" id="sending_limit" name="sending_limit">
                                    @foreach($plan->getSendingLimitSelectOptions() as $limits)
                                        <option value="{{$limits['value']}}" {{ $options['sending_limit'] == $limits['value'] ? 'selected': null }}> {{ $limits['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('sending_limit')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>


                        <div class="show-custom-sending-limit col-12">
                            <p>{!! __('locale.description.sending_speed') !!} </p>
                        </div>

                        <div class="col-12 show-custom-sending-limit">
                            <div class="mb-1">
                                <label for="sending_quota" class="form-label required">{{__('locale.sending_servers.sending_credit')}}</label>
                                <input type="number" id="sending_quota" class="form-control @error('sending_quota') is-invalid @enderror" value="{{ $options['sending_quota'] }}" name="sending_quota" required>
                                @error('sending_quota')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 show-custom-sending-limit">
                            <div class="mb-1">
                                <label for="sending_quota_time" class="form-label required">{{__('locale.sending_servers.time_base')}}</label>
                                <input type="number" id="sending_quota_time" class="form-control @error('sending_quota_time') is-invalid @enderror" value="{{ $options['sending_quota_time'] }}" name="sending_quota_time" required>
                                @error('sending_quota_time')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 show-custom-sending-limit">
                            <div class="mb-1">
                                <label for="sending_quota_time_unit" class="form-label required">{{__('locale.sending_servers.time_unit')}}</label>
                                <select class="form-select" id="sending_quota_time_unit" name="sending_quota_time_unit">
                                    <option value="minute" {{ $options['sending_quota_time_unit'] == 'minute' ? 'selected': null }}> {{__('locale.labels.minute')}}</option>
                                    <option value="hour" {{ $options['sending_quota_time_unit'] == 'hour' ? 'selected': null }}>  {{__('locale.labels.hour')}}</option>
                                    <option value="day" {{ $options['sending_quota_time_unit'] == 'day' ? 'selected': null }}>  {{__('locale.labels.day')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <p>{!! __('locale.description.max_process') !!} </p>
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <label for="max_process" class="form-label required">{{__('locale.plans.number_of_process')}}</label>
                                <select class="form-select" id="max_process" name="max_process">
                                    <option value="1" {{ $options['max_process'] == 1 ? 'selected': null }}> 1</option>
                                    <option value="2" {{ $options['max_process'] == 2 ? 'selected': null }}> 2</option>
                                    <option value="3" {{ $options['max_process'] == 3 ? 'selected': null }}> 3</option>
                                </select>
                            </div>
                            @error('max_process')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary mr-1 mb-1">
                                <i data-feather="save"></i> {{__('locale.buttons.save')}}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
