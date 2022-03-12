@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.top_up'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> {{__('locale.customer.add_unit_to_your_account')}} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <form class="form form-vertical" action="{{ route('user.account.top_up') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">

                                            <p>{!! __('locale.description.add_unit') !!}</p>


                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label for="add_unit" class="form-label required">{{__('locale.labels.per_unit_price')}} = {{ Auth::user()->customer->subscription->plan->getOption('per_unit_price') }} {{ str_replace('{PRICE}', '', Auth::user()->customer->subscription->plan->currency->format) }}</label>
                                                    <div class="input-group input-group-merge mb-2">
                                                        <input type="text" id="add_unit" class="form-control @error('add_unit') is-invalid @enderror" name="add_unit" required>
                                                        <span class="input-group-text update-price">0 {{ str_replace('{PRICE}', '', Auth::user()->customer->subscription->plan->currency->format) }}</span>

                                                        @error('add_unit')
                                                        <p><small class="text-danger">{{ $message }}</small></p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mb-1">
                                                <i data-feather="shopping-cart"></i> {{__('locale.labels.checkout')}}
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


@section('page-script')

    <script>
        let firstInvalid = $('form').find('.is-invalid').eq(0),
            price = 0,
            $get_price = $("#add_unit");

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }


        function get_price() {
            let total_unit = $get_price[0].value;
            let total_price = total_unit * "{{ Auth::user()->customer->subscription->plan->getOption('per_unit_price') }}";
            $('.update-price').text(Math.ceil(total_price) + " {{ str_replace('{PRICE}', '', Auth::user()->customer->subscription->plan->currency->format)}}")
        }

        $get_price.keyup(get_price);

    </script>
@endsection
