@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.create_plan'))

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
                        <h4 class="card-title">{{ __('locale.labels.create_plan') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.sender_id_plan') !!} {{config('app.name')}}</p>

                            <form class="form form-vertical" action="{{ route('admin.senderid.store_plan') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="price" class="form-label required">{{ __('locale.plans.price') }}</label>
                                                <input type="text" id="price" class="form-control @error('price') is-invalid @enderror" value="0" name="price" required placeholder="{{__('locale.labels.required')}}">
                                                @error('price')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="billing_cycle" class="form-label required">{{__('locale.plans.billing_cycle')}}</label>
                                                <select class="form-select" id="billing_cycle" name="billing_cycle">
                                                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected': null }}>  {{__('locale.labels.monthly')}}</option>
                                                    <option value="daily" {{ old('billing_cycle') == 'daily' ? 'selected': null }}> {{__('locale.labels.daily')}}</option>
                                                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected': null }}>  {{__('locale.labels.yearly')}}</option>
                                                    <option value="custom" {{ old('billing_cycle') == 'custom' ? 'selected': null }}>  {{__('locale.labels.custom')}}</option>
                                                </select>
                                            </div>
                                            @error('billing_cycle')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="mb-1">
                                                <label for="frequency_amount" class="form-label required">{{__('locale.plans.frequency_amount')}}</label>
                                                <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" value="{{ old('frequency_amount') }}" name="frequency_amount">
                                                @error('frequency_amount')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="mb-1">
                                                <label for="frequency_unit" class="form-label required">{{__('locale.plans.frequency_unit')}}</label>
                                                <select class="form-select" id="frequency_unit" name="frequency_unit">
                                                    <option value="day"> {{__('locale.labels.day')}}</option>
                                                    <option value="week">  {{__('locale.labels.week')}}</option>
                                                    <option value="month">  {{__('locale.labels.month')}}</option>
                                                    <option value="year">  {{__('locale.labels.year')}}</option>
                                                </select>
                                            </div>
                                            @error('frequency_unit')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="currency_id" class="form-label required">{{__('locale.labels.currency')}}</label>
                                                <select class="form-select select2" id="currency_id" name="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option value="{{$currency->id}}"> {{ $currency->name }} ({{$currency->code}})</option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary me-1 mb-1"><i data-feather="save"></i> {{ __('locale.buttons.save') }}</button>
                                            <button type="reset" class="btn btn-outline-warning mb-1"><i data-feather="refresh-cw"></i> {{ __('locale.buttons.reset') }}</button>
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
            let showCustom = $('.show-custom');
            let billing_cycle = $('#billing_cycle');

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


            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>
@endsection
