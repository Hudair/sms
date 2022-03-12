@extends('layouts/contentLayoutMaster')

@section('title', __('locale.phone_numbers.update_number'))

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
                        <h4 class="card-title">{{ __('locale.phone_numbers.update_number') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.phone_number') !!}</p>

                            <form class="form form-vertical" action="{{ route('admin.phone-numbers.update',  $number->uid) }}" method="post">
                                {{ method_field('PUT') }}
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="number" class="form-label required">{{ __('locale.labels.number') }}</label>
                                                <input type="text" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ $number->number }}" name="number" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('number')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="status" class="form-label required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-select" name="status" id="status">
                                                    <option value="available" {{old('status', isset($number->status) && $number->status == 'available' ? 'selected' : null)}}>{{ __('locale.labels.available') }}</option>
                                                    <option value="assigned" {{old('status', isset($number->status) && $number->status == 'assigned' ? 'selected' : null)}}>{{ __('locale.labels.assigned')}} </option>
                                                </select>
                                            </div>

                                            @error('status')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="capabilities" class="form-label required">{{ __('locale.labels.capabilities') }}</label>
                                                <select class="select2-icons form-select" name="capabilities[]" id="capabilities" multiple="multiple">
                                                    <option value="sms" data-icon="message-square" @if(isset($capabilities) && in_array('sms', $capabilities)) selected @endif>{{ __('locale.labels.sms') }}</option>
                                                    <option value="voice" data-icon="phone-call" @if(isset($capabilities) && in_array('voice', $capabilities)) selected @endif>{{ __('locale.labels.voice')}} </option>
                                                    <option value="mms" data-icon="image" @if(isset($capabilities) && in_array('mms', $capabilities)) selected @endif>{{ __('locale.labels.mms')}} </option>
                                                    <option value="whatsapp" data-icon="message-circle" @if(isset($capabilities) && in_array('whatsapp', $capabilities)) selected @endif>{{ __('locale.labels.whatsapp')}} </option>
                                                </select>
                                            </div>

                                            @error('capabilities')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="price" class="form-label required">{{ __('locale.plans.price') }}</label>
                                                <input type="text" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ isset($number->price) ? $number->price : 0 }}" name="price" required placeholder="{{__('locale.labels.required')}}">
                                                @error('price')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="billing_cycle" class="form-label required">{{__('locale.plans.billing_cycle')}}</label>
                                                <select class="form-select" id="billing_cycle" name="billing_cycle">
                                                    <option value="daily" {{ $number->billing_cycle == 'daily' ? 'selected': null }}> {{__('locale.labels.daily')}}</option>
                                                    <option value="monthly" {{ $number->billing_cycle == 'monthly' ? 'selected': null }}>  {{__('locale.labels.monthly')}}</option>
                                                    <option value="yearly" {{ $number->billing_cycle == 'yearly' ? 'selected': null }}>  {{__('locale.labels.yearly')}}</option>
                                                    <option value="custom" {{ $number->billing_cycle == 'custom' ? 'selected': null }}>  {{__('locale.labels.custom')}}</option>
                                                </select>
                                            </div>
                                            @error('billing_cycle')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>


                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="mb-1">
                                                <label for="frequency_amount" class="form-label required">{{__('locale.plans.frequency_amount')}}</label>
                                                <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" value="{{ $number->frequency_amount }}" name="frequency_amount">
                                                @error('frequency_amount')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="mb-1">
                                                <label for="frequency_unit" class="form-label required">{{__('locale.plans.frequency_unit')}}</label>
                                                <select class="form-select" id="frequency_unit" name="frequency_unit">
                                                    <option value="day" {{ $number->frequency_unit == 'day' ? 'selected': null }}> {{__('locale.labels.day')}}</option>
                                                    <option value="week" {{ $number->frequency_unit == 'week' ? 'selected': null }}>  {{__('locale.labels.week')}}</option>
                                                    <option value="month" {{ $number->frequency_unit == 'month' ? 'selected': null }}>  {{__('locale.labels.month')}}</option>
                                                    <option value="year" {{ $number->frequency_unit == 'year' ? 'selected': null }}>  {{__('locale.labels.year')}}</option>
                                                </select>
                                            </div>
                                            @error('frequency_unit')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="user_id" class="form-label required">{{__('locale.labels.select_customer')}}</label>
                                                <select class="form-select select2" id="user_id" name="user_id">
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" {{ isset($number->user_id) && $number->user_id == $customer->id ? 'selected' : null }}>{{$customer->displayName()}}</option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror

                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="currency_id" class="form-label required">{{__('locale.labels.currency')}}</label>
                                                <select class="form-select select2" id="currency_id" name="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option {{ isset($number->currency_id) && $number->currency_id == $currency->id ? 'selected' : null }} value="{{$currency->id}}"> {{ $currency->name }}
                                                            ({{$currency->code}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mb-1"><i data-feather="save"></i> {{ __('locale.buttons.save') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </form>
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
        $(document).ready(function () {

            let showCustom = $('.show-custom'),
                billing_cycle = $('#billing_cycle'),
                selectIcons = $('.select2-icons');


            if (billing_cycle.val() === 'custom') {
                showCustom.show();
            } else {
                showCustom.hide();
            }

            billing_cycle.on('change', function () {
                if (billing_cycle.val() === 'custom') {
                    showCustom.show();
                } else {
                    showCustom.hide();
                }
            });

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

            // Select With Icon
            selectIcons.each(function () {
                let $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    dropdownAutoWidth: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    dropdownParent: $this.parent(),
                    templateResult: iconFormat,
                    templateSelection: iconFormat,
                    escapeMarkup: function (es) {
                        return es;
                    }
                });
            });

            // Format icon
            function iconFormat(icon) {
                if (!icon.id) {
                    return icon.text;
                }

                return feather.icons[$(icon.element).data('icon')].toSvg() + icon.text;
            }

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>
@endsection
