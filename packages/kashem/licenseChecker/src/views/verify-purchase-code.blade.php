@php
    $configData = Helper::applClasses();
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.login'))

@section('page-style')
    {{-- Page Css files --}}
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
            <div class="col-lg-4 d-none d-lg-flex align-items-center p-0">
                <div class="w-100 d-lg-flex align-items-center justify-content-center">
                    <img class="img-fluid w-100" src="{{asset('images/pages/create-account.svg')}}" alt="{{config('app.name')}}"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Login-->
            <div class="col-lg-8 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 ">
                <div class="width-700 mx-auto card px-2 py-2">
                    <h2 class="card-title fw-bold mb-1">Verify Product code</h2>
                    <p class="card-text mb-2">To get your purchase code please check this <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Where Is My Purchase Code?</a> </p>

                    <form class="auth-login-form" action="{{route('verify.license')}}" method="post">
                        @csrf
                        <div class="mb-1">
                            <label class="form-label" for="application_url">Application URL</label>
                            <input  type="text" class="form-control @error('application_url') is-invalid @enderror" name="application_url"  value="{{env('APP_URL')}}" required>

                            @error('application_url')
                            <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                                <div class="alert-body d-flex align-items-center">
                                    <i data-feather="info" class="me-50"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label class="form-label" for="purchase_code">Purchase Code</label>
                            <input  type="text" class="form-control @error('purchase_code') is-invalid @enderror" name="purchase_code" required autofocus>

                            @error('purchase_code')
                            <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                                <div class="alert-body d-flex align-items-center">
                                    <i data-feather="info" class="me-50"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100" tabindex="4">Verify Now</button>
                    </form>

                </div>
            </div>
            <!-- /Login-->
        </div>
    </div>
@endsection
