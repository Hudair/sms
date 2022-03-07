@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.pay_payment'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="" role="form" method="post" action="{{ $post_url }}">
                                @csrf

                                <div class="form-group">
                                    <label for="owner" class="required">{{ __('locale.labels.name_on_the_card') }}</label>
                                    <input type="text" id="owner" class="form-control @error('owner') is-invalid @enderror" value="{{ old('owner') }}" name="owner" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                    @error('owner')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cardNumber" class="required">{{ __('locale.labels.card_number') }}</label>
                                            <input type="number" id="cardNumber" class="form-control @error('cardNumber') is-invalid @enderror" value="{{ old('cardNumber') }}" name="cardNumber" required placeholder="{{__('locale.labels.required')}}">
                                            @error('cardNumber')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="cvv" class="required">{{ __('locale.labels.cvv') }}</label>
                                            <input type="number" id="cvv" class="form-control @error('cvv') is-invalid @enderror" value="{{ old('cvv') }}" name="cvv" required placeholder="{{__('locale.labels.required')}}">
                                            @error('cvv')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="required">{{ __('locale.labels.expiration_date') }}</label><br>
                                            <select class="form-control" id="expiration_month" name="expiration_month" style="float: left; width: 100px; margin-right: 10px;">
                                                @foreach($months as $k=>$v)
                                                    <option value="{{ $k }}" {{ old('expiration_month') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control" id="expiration_year" name="expiration_year" style="float: left; width: 100px;">

                                                @for($i = date('Y'); $i <= (date('Y') + 15); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary themeButton" id="confirm-purchase">{{ __('locale.labels.pay_payment') }}</button>
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

@section('page-script')
    <!-- Page js files -->
    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>

@endsection
