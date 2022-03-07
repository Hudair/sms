@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.verify_email_address'))

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
@endsection
@section('content')
    <section class="row flexbox-container">
        <div class="col-xl-7 col-md-9 col-10 d-flex justify-content-center px-0">
            <div class="card bg-authentication rounded-0 mb-0">
                <div class="row m-0">
                    <div class="col-lg-6 d-lg-block d-none text-center align-self-center">
                        <img src="{{ asset('images/pages/lock-screen.png') }}" alt="logo">
                    </div>
                    <div class="col-lg-6 col-12 p-0">
                        <div class="card rounded-0 mb-0 px-2 py-1">
                            <div class="card-header pb-1">
                                <div class="card-title">
                                    <h4 class="mb-0">{{ __('locale.auth.verify_email_address') }}</h4>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ __('locale.auth.fresh_verification_link') }}
                                        </div>
                                    @endif
                                    <p>{{ __('locale.auth.resend_verification_link') }}</p>
                                    <p>{{ __('If you did not receive the email') }}, <a href="{{ route('verification.send') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('click here to request another') }}</a>.</p>
                                    <form id="resend-form" action="{{ route('verification.send') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
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
