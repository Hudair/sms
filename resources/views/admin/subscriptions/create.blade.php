@extends('layouts/contentLayoutMaster')

@section('title', __('locale.buttons.new_subscription'))

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
                        <h4 class="card-title">{{ __('locale.buttons.new_subscription') }} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.subscriptions.store') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="user_id" class="form-label required">{{__('locale.labels.select_customer')}}</label>
                                                <select class="form-select customer" name="user_id">
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" {{ Request::get('customer_id') == $customer->id ? 'selected': null }}>
                                                            {{$customer->displayName()}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @error('user_id')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="plan_id" class="form-label required">{{__('locale.labels.select_plan')}}</label>
                                                <select class="form-select plan" name="plan_id">
                                                    @foreach($plans as $plan)
                                                        <option value="{{$plan->id}}">{{ htmlspecialchars($plan->name)."|".htmlspecialchars(\App\Library\Tool::format_price($plan->price, $plan->currency->format)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @error('plan_id')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="end_period_last_days" class="form-label required">{{ __('locale.subscription.subscription_period_end') }}</label>
                                                <input type="number" id="end_period_last_days" class="form-control text-right @error('end_period_last_days') is-invalid @enderror" value="10" name="end_period_last_days" required placeholder="{{__('locale.labels.required')}}">

                                                @error('end_period_last_days')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mb-1"><i data-feather="save"></i> {{ __('locale.buttons.create') }}</button>
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

            function formatSearch(item) {
                let selectionText = item.text.split("|");
                return $('<span>' + selectionText[0] + '</br><b>' + selectionText[1] + '</b>' + '</span>');
            }

            function formatSelected(item) {
                let selectionText = item.text.split("|");
                return $('<span>' + selectionText[0].substring(0, 100) + '</span>');
            }

            $(".customer").each(function () {
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

            $(".plan").each(function () {
                let $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    // the following code is used to disable x-scrollbar when click in select input and
                    // take 100% width in responsive also
                    dropdownAutoWidth: true,
                    width: '100%',
                    templateResult: formatSearch,
                    templateSelection: formatSelected,
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
