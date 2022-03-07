@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.new_conversion'))


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
                        <h4 class="card-title">{{ __('locale.labels.new_conversion') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('customer.chatbox.sent') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-12">

                                        <div class="form-group">
                                            <label for="sender_id" class="required">{{__('locale.labels.phone')}}</label>
                                            <select class="form-control select2" id="sender_id" name="sender_id">
                                                @foreach($phone_numbers as $number)
                                                    <option value="{{$number->number}}"> {{ $number->number }}</option>
                                                @endforeach
                                            </select>

                                            @error('sender_id')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="recipient" class="required">{{ __('locale.labels.recipient') }}</label>
                                            <input type="text"
                                                   id="recipient"
                                                   class="form-control @error('recipient') is-invalid @enderror"
                                                   value="{{ old('recipient') }}"
                                                   name="recipient"
                                                   required
                                                   placeholder="{{__('locale.labels.required')}}"
                                            >
                                            @error('recipient')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="message" class="required">{{ __('locale.labels.message') }}</label>
                                            <textarea class="form-control" rows="4" required name="message">{{old('message')}}</textarea>
                                            @error('message')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" name="sms_type" value="plain">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 pull-right">
                                            <i class="feather icon-send"></i> {{__('locale.buttons.send')}}
                                        </button>
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

    </script>
@endsection
