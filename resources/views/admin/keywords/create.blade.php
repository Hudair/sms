@extends('layouts/contentLayoutMaster')
@if(isset($keyword))
    @section('title', __('locale.keywords.update_keyword'))
@else
    @section('title', __('locale.keywords.create_new_keyword'))
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
                        <h4 class="card-title">@if(isset($keyword)) {{ __('locale.keywords.update_keyword') }} @else {{ __('locale.keywords.create_new_keyword') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.keywords') !!} {{config('app.name')}}</p>

                            <form class="form form-vertical" @if(isset($keyword)) action="{{ route('admin.keywords.update',  $keyword->uid) }}" @else action="{{ route('admin.keywords.store') }}" @endif method="post" enctype="multipart/form-data">
                                @if(isset($keyword))
                                    {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="title" class="required">{{ __('locale.labels.title') }}</label>
                                                <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title',  isset($keyword->title) ? $keyword->title : null) }}" name="title" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keyword_name" class="required">{{ __('locale.labels.keyword') }}</label>
                                                <input type="text" id="keyword_name" class="form-control @error('keyword_name') is-invalid @enderror" value="{{ old('keyword_name',  isset($keyword->keyword_name) ? $keyword->keyword_name : null) }}" name="keyword_name" required placeholder="{{__('locale.labels.required')}}">
                                                @error('keyword_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="sender_id">{{ __('locale.labels.sender_id') }}</label>
                                                <input type="text" id="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ old('sender_id',  isset($keyword->sender_id) ? $keyword->sender_id : null) }}" name="sender_id">
                                                @error('sender_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reply_text">{{__('locale.keywords.reply_text_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_text"> {{old('reply_text', isset($keyword->reply_text) ? $keyword->reply_text : null)}} </textarea>

                                                @error('reply_text')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reply_voice">{{__('locale.keywords.reply_voice_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_voice"> {{old('reply_voice', isset($keyword->reply_voice) ? $keyword->reply_voice : null)}} </textarea>

                                                @error('reply_voice')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if(isset($keyword->reply_mms))
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('locale.labels.mms_file') }}</label>
                                                    <p><a href="{{$keyword->reply_mms}}" target="_blank">{{$keyword->reply_mms}}</a> </p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="reply_mms">{{ __('locale.keywords.reply_mms_recipient') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="reply_mms" class="custom-file-input" id="reply_mms" accept="image/*">
                                                    <label class="custom-file-label" for="reply_mms" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>

                                                    @error('reply_mms')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="status" class="required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="available" {{old('status', isset($keyword->status) && $keyword->status == 'available' ? 'selected' : null)}}>{{ __('locale.labels.available') }}</option>
                                                    <option value="assigned" {{old('status', isset($keyword->status) && $keyword->status == 'assigned' ? 'selected' : null)}}>{{ __('locale.labels.assigned')}} </option>
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
                                                <label for="user_id">{{__('locale.labels.select_customer')}}</label>
                                                <select class="form-control select2" name="user_id">
                                                    <option value="0" {{old('user_id', isset($keyword->user_id) && $keyword->user_id == '0' ? 'selected' : null)}}>{{__('locale.labels.none')}}</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" {{old('user_id', isset($keyword->user_id) && $keyword->user_id == $customer->id ? 'selected' : null)}}>{{$customer->displayName()}}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>

                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="price" class="required">{{ __('locale.plans.price') }}</label>
                                                <input type="text" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price',  isset($keyword->price) ? $keyword->price : null) }}" name="price" required placeholder="{{__('locale.labels.required')}}">
                                                @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="currency_id" class="required">{{__('locale.labels.currency')}}</label>
                                                <select class="form-control select2" id="currency_id" name="currency_id">
                                                    @foreach($currencies as $currency)
                                                        <option {{ old('currency_id', isset($keyword->currency_id) && $keyword->currency_id == $currency->id ? 'selected' : null) }} value="{{$currency->id}}"> {{ $currency->name }}
                                                            ({{$currency->code}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                            @error('currency_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="billing_cycle" class="required">{{__('locale.labels.renew')}}</label>
                                                <select class="form-control" id="billing_cycle" name="billing_cycle">
                                                    <option value="yearly" {{old('billing_cycle', isset($keyword->billing_cycle) && $keyword->billing_cycle == 'yearly' ? 'selected' : null)}}> {{__('locale.labels.yearly')}}</option>
                                                    <option value="daily" {{old('billing_cycle', isset($keyword->billing_cycle) && $keyword->billing_cycle == 'daily' ? 'selected' : null)}}> {{__('locale.labels.daily')}}</option>
                                                    <option value="monthly" {{old('billing_cycle', isset($keyword->billing_cycle) && $keyword->billing_cycle == 'monthly' ? 'selected' : null)}}> {{__('locale.labels.monthly')}}</option>
                                                    <option value="custom" {{old('billing_cycle', isset($keyword->billing_cycle) && $keyword->billing_cycle == 'custom' ? 'selected' : null)}}> {{__('locale.labels.custom')}}</option>
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
                                                <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" name="frequency_amount" value="{{ old('frequency_amount',  isset($keyword->frequency_amount) ? $keyword->frequency_amount : null) }}" >
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
                                                    <option value="day" {{old('frequency_unit', isset($keyword->frequency_unit) && $keyword->frequency_unit == 'day' ? 'selected' : null)}}> {{__('locale.labels.day')}}</option>
                                                    <option value="week" {{old('frequency_unit', isset($keyword->frequency_unit) && $keyword->frequency_unit == 'week' ? 'selected' : null)}}> {{__('locale.labels.week')}}</option>
                                                    <option value="month" {{old('frequency_unit', isset($keyword->frequency_unit) && $keyword->frequency_unit == 'month' ? 'selected' : null)}}> {{__('locale.labels.month')}}</option>
                                                    <option value="year" {{old('frequency_unit', isset($keyword->frequency_unit) && $keyword->frequency_unit == 'year' ? 'selected' : null)}}> {{__('locale.labels.year')}}</option>
                                                </select>
                                            </fieldset>
                                            @error('frequency_unit')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                            @if( ! isset($keyword))
                                                <button type="reset" class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-refresh-cw"></i>
                                                    {{ __('locale.buttons.reset') }}
                                                </button>
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

        });
    </script>
@endsection
