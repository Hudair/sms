@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="{{route('login')}}">
                <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}"/>
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="col-lg-3 d-none d-lg-flex align-items-center p-0">
                <div class="w-100 d-lg-flex align-items-center justify-content-center">
                    <img class="img-fluid w-100" src="{{asset('images/pages/create-account.svg')}}" alt="{{config('app.name')}}"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Register-->
            <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                <div class="width-800 mx-auto card px-2 py-2">
                    <form class="" role="form" method="post" action="{{ $post_url }}">
                        @csrf

                        <div class="row">

                            <div class="col-12">

                                <div class="mb-1">
                                    <label for="owner" class="required form-label">{{ __('locale.labels.name_on_the_card') }}</label>
                                    <input type="text" id="owner" class="form-control @error('owner') is-invalid @enderror" value="{{ old('owner') }}" name="owner" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                    @error('owner')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                    <label for="cardNumber" class="required form-label">{{ __('locale.labels.card_number') }}</label>
                                    <input type="number" id="cardNumber" class="form-control @error('cardNumber') is-invalid @enderror" value="{{ old('cardNumber') }}" name="cardNumber" required placeholder="{{__('locale.labels.required')}}">
                                    @error('cardNumber')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mb-1">
                                    <label for="cvv" class="required form-label">{{ __('locale.labels.cvv') }}</label>
                                    <input type="number" id="cvv" class="form-control @error('cvv') is-invalid @enderror" value="{{ old('cvv') }}" name="cvv" required placeholder="{{__('locale.labels.required')}}">
                                    @error('cvv')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="mb-1">
                                    <label class="required form-label">{{ __('locale.labels.expiration_date') }}</label><br>
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

                        <div class="mb-1">
                            <button type="submit" class="btn btn-primary themeButton" id="confirm-purchase">{{ __('locale.labels.pay_payment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
