@extends('layouts/contentLayoutMaster')
@if(isset($number))
    @section('title', __('locale.phone_numbers.update_number'))
@else
    @section('title', __('locale.phone_numbers.add_new_number'))
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
                        <h4 class="card-title">@if(isset($number)) {{ __('locale.phone_numbers.update_number') }} @else {{ __('locale.phone_numbers.add_new_number') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.phone_number') !!}</p>

                            <form class="form form-vertical" @if(isset($number)) action="{{ route('admin.phone-numbers.update',  $number->uid) }}" @else action="{{ route('admin.phone-numbers.store') }}" @endif method="post">
                                @if(isset($number))
                                    {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="number" class="required">{{ __('locale.labels.number') }}</label>
                                                <input type="text" id="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number',  isset($number->number) ? $number->number : null) }}" name="number" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('number')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="status" class="required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="available" {{old('status', isset($number->status) && $number->status == 'available' ? 'selected' : null)}}>{{ __('locale.labels.available') }}</option>
                                                    <option value="assigned" {{old('status', isset($number->status) && $number->status == 'assigned' ? 'selected' : null)}}>{{ __('locale.labels.assigned')}} </option>
                                                </select>
                                            </fieldset>

                                            @error('status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="capabilities" class="required">{{ __('locale.labels.capabilities') }}</label>
                                                <select class="select2 form-control" name="capabilities[]" id="capabilities"  multiple="multiple">
                                                    <option value="sms" @if(isset($capabilities) && in_array('sms', $capabilities)) selected @elseif(!isset($number)) selected @endif>{{ __('locale.labels.sms') }}</option>
                                                    <option value="voice" @if(isset($capabilities) && in_array('voice', $capabilities)) selected @endif>{{ __('locale.labels.voice')}} </option>
                                                    <option value="mms"  @if(isset($capabilities) && in_array('mms', $capabilities)) selected @endif>{{ __('locale.labels.mms')}} </option>
                                                    <option value="whatsapp"  @if(isset($capabilities) && in_array('whatsapp', $capabilities)) selected @endif>{{ __('locale.labels.whatsapp')}} </option>
                                                </select>
                                            </fieldset>

                                            @error('capabilities')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="price" class="required">{{ __('locale.plans.price') }}</label>
                                                <input type="text" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ isset($number->price) ? $number->price : 0 }}" name="price" required placeholder="{{__('locale.labels.required')}}">
                                                @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="user_id" class="required">{{__('locale.labels.select_customer')}}</label>
                                                <select class="form-control select2" name="user_id">
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" {{ isset($number->user_id) && $number->user_id == $customer->id ? 'selected' : null }}>{{$customer->displayName()}}</option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="currency_id" class="required">{{__('locale.labels.currency')}}</label>
                                                <select class="form-control select2" id="currency_id" name="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option {{ isset($number->currency_id) && $number->currency_id == $currency->id ? 'selected' : null }} value="{{$currency->id}}"> {{ $currency->name }}
                                                            ({{$currency->code}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('currency_id')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                            @if( ! isset($number))
                                                <button type="reset" class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-refresh-cw"></i> {{ __('locale.buttons.reset') }}</button>
                                            @endif
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

            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
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
