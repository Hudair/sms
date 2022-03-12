@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.two_factor_authentication'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="{{route('login')}}">
                <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}"/>
            </a>
            <!-- /Brand logo-->

            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    @if($configData['theme'] === 'dark')
                        <img class="img-fluid" src="{{asset('images/pages/two-steps-verification-illustration-dark.svg')}}" alt="two steps verification"/>
                    @else
                        <img class="img-fluid" src="{{asset('images/pages/two-steps-verification-illustration.svg')}}" alt="two steps verification"/>
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- two steps verification v2-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title fw-bolder mb-1">{{ __('locale.auth.two_factor_authentication') }}</h2>
                    <p class="card-text mb-75">We sent a verification code to your email. Enter the code from the mobile in the field below.</p>
                    @if(Auth::check())
                        <p class="card-text fw-bolder mb-2">{{ preg_replace_callback('/(\w)(.*?)(\w)(@.*?)$/s', function ($matches){
    return $matches[1].preg_replace("/\w/", "*", $matches[2]).$matches[3].$matches[4];
}, Auth::user()->email) }}</p>
                    @endif
                    <form method="POST" action="{{ route('verify.store') }}" class="mt-2">
                        @csrf
                        <h6>Type your 6 digit security code</h6>
                        <div class="auth-input-wrapper d-flex align-items-center justify-content-between">
                            <input id="two_factor_code" type="number" class="form-control  text-center numeral-mask mb-1 @error('two_factor_code') is-invalid @enderror" name="two_factor_code" value="{{ old('two_factor_code') }}" required autocomplete="two_factor_code" maxlength="6" autofocus>

                            @error('two_factor_code')
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                            @enderror
                        </div>
                        <button class="btn btn-primary w-100" type="submit" tabindex="4">{{ __('locale.auth.verify') }}</button>
                    </form>
                    <p class="text-center mt-2">
                        <span>Didn&rsquo;t get the code?</span>
                        <a href="{{ route('verify.resend') }}">Resend</a>
                        <span>or</span>
                        <a href="{{ route('verify.backup')}}">{{ __('locale.auth.verify_with_backup_code') }}</a>

                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
