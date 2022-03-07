@extends('layouts/contentLayoutMaster')

@section('title', __('locale.plans.add_new_plan'))

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
                        <h4 class="card-title"> {{__('locale.plans.add_new_plan')}} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p>{!! __('locale.description.plan_details') !!} </p>
                            <div class="form-body">
                                <form class="form form-vertical" action="{{ route('admin.plans.store') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name" class="required">{{__('locale.labels.name')}}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" name="name" required>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="price"  class="required">{{__('locale.plans.price')}}</label>
                                                <input type="text" id="price" class="form-control text-right @error('price') is-invalid @enderror" value="{{ old('price') }}" name="price" required>
                                                @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="billing_cycle" class="required">{{__('locale.plans.billing_cycle')}}</label>
                                                <select class="form-control" id="billing_cycle" name="billing_cycle">
                                                    <option value="daily" {{ old('billing_cycle') == 'daily' ? 'selected': null }}> {{__('locale.labels.daily')}}</option>
                                                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected': null }}>  {{__('locale.labels.monthly')}}</option>
                                                    <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected': null }}>  {{__('locale.labels.yearly')}}</option>
                                                    <option value="custom" {{ old('billing_cycle') == 'custom' ? 'selected': null }}>  {{__('locale.labels.custom')}}</option>
                                                </select>
                                            </fieldset>
                                            @error('billing_cycle')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="form-group">
                                                <label for="frequency_amount" class="required">{{__('locale.plans.frequency_amount')}}</label>
                                                <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" value="{{ old('frequency_amount') }}" name="frequency_amount">
                                                @error('frequency_amount')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <fieldset class="form-group">
                                                <label for="frequency_unit" class="required">{{__('locale.plans.frequency_unit')}}</label>
                                                <select class="form-control" id="frequency_unit" name="frequency_unit">
                                                    <option value="day"> {{__('locale.labels.day')}}</option>
                                                    <option value="week">  {{__('locale.labels.week')}}</option>
                                                    <option value="month">  {{__('locale.labels.month')}}</option>
                                                    <option value="year">  {{__('locale.labels.year')}}</option>
                                                </select>
                                            </fieldset>
                                            @error('frequency_unit')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="currency_id" class="required">{{__('locale.labels.currency')}}</label>
                                                <select class="form-control select2" id="currency_id" name="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option value="{{$currency->id}}"> {{ $currency->name }}
                                                            ({{$currency->code}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                            @error('currency_id')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <fieldset>
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" value="true" name="tax_billing_required">
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.plans.billing_information_required')}}</span>
                                                </div>
                                                <p>
                                                    <small class="text-muted">{{__('locale.plans.ask_tax_billing_information_subscribing_plan')}}</small>
                                                </p>

                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <fieldset>
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" value="true" name="is_popular">
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.labels.is_popular')}}</span>
                                                </div>
                                                <p>
                                                    <small class="text-muted">{{__('locale.plans.set_this_plan_as_popular')}}</small>
                                                </p>

                                            </fieldset>
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
        let showCustom = $('.show-custom');
        let billing_cycle = $('#billing_cycle');

        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if ( firstInvalid.length ) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

        if (billing_cycle.val() === 'custom'){
            showCustom.show();
        }else {
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
        $(".select2").select2({
            // the following code is used to disable x-scrollbar when click in select input and
            // take 100% width in responsive also
            dropdownAutoWidth: true,
            width: '100%'
        });

    </script>
@endsection
