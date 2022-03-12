@extends('layouts/contentLayoutMaster')

@if(isset($coverage))
    @section('title', __('locale.buttons.update_coverage'))
@else
    @section('title', __('locale.buttons.add_coverage'))
@endif

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">

                        <h4 class="card-title">@if(isset($coverage)) {{ __('locale.buttons.update_coverage') }} @else
                                {{ __('locale.buttons.add_coverage') }} @endif </h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <p>{!! __('locale.description.pricing_intro') !!}</p>
                            <div class="form-body">
                                <form class="form form-vertical" @if(isset($coverage)) action="{{ route('admin.plans.settings.edit_coverage', ['plan' => $plan->uid, 'coverage' => $coverage->uid]) }}" @else action="{{ route('admin.plans.settings.coverage', $plan->uid) }}" @endif method="post">
                                    @csrf
                                    <div class="row">

                                        @if(isset($coverage))
                                            <input type="hidden" value="{{ $coverage->country_id }}" name="country">
                                        @else
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label for="country" class="form-label required">{{__('locale.labels.country')}}</label>
                                                    <select class="form-select select2" id="country" name="country">
                                                        @foreach($countries as $country)
                                                            <option value="{{$country->id}}"> {{ $country->name }} (+{{$country->country_code}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('country')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="plain_sms" class="required form-label">{{__('locale.labels.plain_sms')}}</label>
                                                <input type="number" id="plain_sms" class="form-control @error('plain_sms') is-invalid @enderror"
                                                       value="{{ old('plain_sms',  $options['plain_sms'] ?? null) }}"
                                                       name="plain_sms" required>
                                                @error('plain_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="receive_plain_sms" class="required form-label">{{__('locale.labels.receive')}} {{__('locale.labels.plain_sms')}}</label>
                                                <input type="number" id="receive_plain_sms" class="form-control @error('receive_plain_sms') is-invalid @enderror"
                                                       value="{{ old('receive_plain_sms',  $options['receive_plain_sms'] ?? null) }}"
                                                       name="receive_plain_sms" required>
                                                @error('receive_plain_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="voice_sms" class="required form-label">{{__('locale.labels.voice_sms')}}</label>
                                                <input type="number" id="voice_sms" class="form-control @error('voice_sms') is-invalid @enderror"
                                                       value="{{ old('voice_sms',  $options['voice_sms'] ?? null) }}"
                                                       name="voice_sms" required>
                                                @error('voice_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="receive_voice_sms" class="required form-label">{{__('locale.labels.receive')}} {{__('locale.labels.voice_sms')}}</label>
                                                <input type="number" id="receive_voice_sms" class="form-control @error('receive_voice_sms') is-invalid @enderror"
                                                       value="{{ old('receive_voice_sms',  $options['receive_voice_sms'] ?? null) }}"
                                                       name="receive_voice_sms" required>
                                                @error('receive_voice_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="mms_sms" class="required form-label">{{__('locale.labels.mms_sms')}}</label>
                                                <input type="number" id="mms_sms" class="form-control @error('mms_sms') is-invalid @enderror"
                                                       value="{{ old('mms_sms',  $options['mms_sms'] ?? null) }}"
                                                       name="mms_sms" required>
                                                @error('mms_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="receive_mms_sms" class="required form-label">{{__('locale.labels.receive')}} {{__('locale.labels.mms_sms')}}</label>
                                                <input type="number" id="receive_mms_sms" class="form-control @error('receive_mms_sms') is-invalid @enderror"
                                                       value="{{ old('receive_mms_sms',  $options['receive_mms_sms'] ?? null) }}"
                                                       name="receive_mms_sms" required>
                                                @error('receive_mms_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="whatsapp_sms" class="required form-label">{{__('locale.labels.whatsapp_sms')}}</label>
                                                <input type="number" id="whatsapp_sms" class="form-control @error('whatsapp_sms') is-invalid @enderror"
                                                       value="{{ old('whatsapp_sms',  $options['whatsapp_sms'] ?? null) }}"
                                                       name="whatsapp_sms" required>
                                                @error('whatsapp_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6 col-12">
                                            <div class="mb-1">
                                                <label for="receive_whatsapp_sms" class="required form-label">{{__('locale.labels.receive')}} {{__('locale.labels.whatsapp_sms')}}</label>
                                                <input type="number" id="receive_whatsapp_sms" class="form-control @error('receive_whatsapp_sms') is-invalid @enderror"
                                                       value="{{ old('receive_whatsapp_sms',  $options['receive_whatsapp_sms'] ?? null) }}"
                                                       name="receive_whatsapp_sms" required>
                                                @error('receive_whatsapp_sms')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
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


            </div>
        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>

        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

        // Basic Select2 select
        $(".select2").each(function () {
            let $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $this.parent()
            });
        });

    </script>
@endsection
