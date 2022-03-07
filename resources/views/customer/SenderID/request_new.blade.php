@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.request_for_new_one'))

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
                        <h4 class="card-title">{{ __('locale.labels.request_for_new_one') }} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('customer.senderid.store') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="sender_id" class="required">{{ __('locale.menu.Sender ID') }}</label>
                                                <input type="text" id="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ old('sender_id') }}" name="sender_id" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('sender_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="plan" class="required">{{__('locale.labels.select_plan')}}</label>
                                                <select class="form-control select2" name="plan">
                                                    @foreach($sender_id_plans as $plan)
                                                        <option value="{{$plan->id}}">
                                                            {{$plan->displayFrequencyTime()}}
                                                            ({{$plan->price == 0 ? __('locale.labels.free') : \App\Library\Tool::format_price($plan->price, $plan->currency->format)}})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('plan')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-send"></i> {{ __('locale.buttons.send') }}</button>
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
