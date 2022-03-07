@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.login'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
@endsection

@section('content')
    <section class="row flexbox-container">
        <div class="col-xl-8 col-11 d-flex justify-content-center">
            <div class="card bg-authentication rounded-0 mb-0">
                <div class="row m-0">
                    <div class="col-lg-5 d-lg-block d-none text-center align-self-center px-1 py-0">
                        <img src="{{ asset('images/pages/reset-password.png') }}" alt="branding logo">
                    </div>
                    <div class="col-lg-7 col-12 p-0">
                        <div class="card rounded-0 mb-0 px-2">
                            <div class="card-header pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">Verify your purchase code</h4>
                                </div>
                            </div>

                            <p class="px-2">To get your purchase code please check this <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Purchase Code</a></p>

                            <div class="card-content">
                                <div class="card-body pt-1">
                                    <form method="POST" action="{{ route('verify.license') }}">
                                        @csrf
                                        <fieldset class="form-label-group form-group position-relative">

                                            <input id="application_url" type="url" class="form-control @error('application_url') is-invalid @enderror" name="application_url" placeholder="Application URL" value="{{ config('app.url') }}" required autocomplete="application_url" autofocus>
                                            <label for="application_url">Application URL</label>
                                            @error('application_url')
                                            <span class="invalid-feedback" role="alert">
                                                <span>{{ $message }}</span>
                                            </span>
                                            @enderror

                                        </fieldset>
                                        <fieldset class="form-label-group form-group position-relative">

                                            <input id="purchase_code" type="text" class="form-control @error('purchase_code') is-invalid @enderror" name="purchase_code" placeholder="Purchase Code" value="{{ old('purchase_code') }}" required autocomplete="purchase_code">
                                            <label for="purchase_code">Purchase Code</label>
                                            @error('purchase_code')
                                            <span class="invalid-feedback" role="alert">
                                                <span>{{ $message }}</span>
                                            </span>
                                            @enderror

                                        </fieldset>

                                        <button type="submit" class="btn btn-primary float-right btn-inline mb-4">{{ __('locale.buttons.update') }}</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
