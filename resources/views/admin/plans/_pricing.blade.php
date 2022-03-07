<div class="col-12">
    <div class="form-body">


        <form class="form form-vertical" action="{{ route('admin.plans.settings.pricing', $plan->uid) }}" method="post">
            @csrf

            <p>{!! __('locale.description.per_unit_price')!!}</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="per_unit_price" class="required">{{__('locale.labels.per_unit_price')}}</label>
                        <div class="input-group">
                            <input type="text" id="per_unit_price" class="form-control @error('per_unit_price') is-invalid @enderror" value="{{ $options['per_unit_price'] }}" name="per_unit_price" required>
                            @error('per_unit_price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="input-group-append">
                                <span class="input-group-text">{{ $plan->currency->code }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p>{!! __('locale.description.pricing_intro') !!}</p>
            <div class="row">

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="plain_sms" class="required">{{__('locale.labels.plain_sms')}}</label>
                        <input type="number" id="plain_sms" class="form-control @error('plain_sms') is-invalid @enderror" value="{{ $options['plain_sms'] }}" name="plain_sms" required>
                        @error('plain_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="receive_plain_sms" class="required">{{__('locale.labels.receive')}} {{__('locale.labels.plain_sms')}}</label>
                        <input type="number" id="receive_plain_sms" class="form-control @error('receive_plain_sms') is-invalid @enderror" value="{{ $options['receive_plain_sms'] }}" name="receive_plain_sms" required>
                        @error('receive_plain_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="voice_sms" class="required">{{__('locale.labels.voice_sms')}}</label>
                        <input type="number" id="voice_sms" class="form-control @error('voice_sms') is-invalid @enderror" value="{{ $options['voice_sms'] }}" name="voice_sms" required>
                        @error('voice_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="receive_voice_sms" class="required">{{__('locale.labels.receive')}} {{__('locale.labels.voice_sms')}}</label>
                        <input type="number" id="receive_voice_sms" class="form-control @error('receive_voice_sms') is-invalid @enderror" value="{{ $options['receive_voice_sms'] }}" name="receive_voice_sms" required>
                        @error('receive_voice_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="mms_sms" class="required">{{__('locale.labels.mms_sms')}}</label>
                        <input type="number" id="mms_sms" class="form-control @error('mms_sms') is-invalid @enderror" value="{{ $options['mms_sms'] }}" name="mms_sms" required>
                        @error('mms_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="receive_mms_sms" class="required">{{__('locale.labels.receive')}} {{__('locale.labels.mms_sms')}}</label>
                        <input type="number" id="receive_mms_sms" class="form-control @error('receive_mms_sms') is-invalid @enderror" value="{{ $options['receive_mms_sms'] }}" name="receive_mms_sms" required>
                        @error('receive_mms_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="whatsapp_sms" class="required">{{__('locale.labels.whatsapp_sms')}}</label>
                        <input type="number" id="whatsapp_sms" class="form-control @error('whatsapp_sms') is-invalid @enderror" value="{{ $options['whatsapp_sms'] }}" name="whatsapp_sms" required>
                        @error('whatsapp_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="receive_whatsapp_sms" class="required">{{__('locale.labels.receive')}} {{__('locale.labels.whatsapp_sms')}}</label>
                        <input type="number" id="receive_whatsapp_sms" class="form-control @error('receive_whatsapp_sms') is-invalid @enderror" value="{{ $options['receive_whatsapp_sms'] }}" name="receive_whatsapp_sms" required>
                        @error('receive_whatsapp_sms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary mr-1 mb-1">
                        <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
