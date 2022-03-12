@php
    $configData = Helper::applClasses();
@endphp

@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.verify_email_address'))

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
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    @if($configData['theme'] === 'dark')
                        <img class="img-fluid" src="{{asset('images/pages/login-v2-dark.svg')}}" alt="{{config('app.name')}}"/>
                    @else
                        <img class="img-fluid" src="{{asset('images/pages/login-v2.svg')}}" alt="{{config('app.name')}}"/>
                    @endif
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Forgot password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ __('locale.auth.fresh_verification_link') }}
                        </div>
                    @endif

                    <h2 class="card-title fw-bold mb-1">{{ __('locale.auth.verify_email_address') }}</h2>
                    <p class="card-text mb-2">{{ __('locale.auth.resend_verification_link') }}</p>
                        <form id="resend-form" action="{{ route('verification.send') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    <p class="card-text mt-2">{{ __('If you did not receive the email') }}, <a href="{{ route('verification.send') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('click here to request another') }}</a>
                    </p>
                </div>
            </div>
            <!-- /Forgot password-->

        </div>
    </div>
@endsection
