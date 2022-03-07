@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.verify_with_backup_code'))

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
          <img src="{{ asset('images/pages/register.jpg') }}" alt="logo">
        </div>
        <div class="col-lg-6 col-12 p-0">
          <div class="card rounded-0 mb-0 px-2 py-1">
            <div class="card-header pb-1">
              <div class="card-title">
                <h4 class="mb-0">{{ __('locale.auth.verify_with_backup_code') }}</h4>
              </div>
            </div>
            <div class="card-content">
              <div class="card-body">

                <p>If you lost your device or lose your email account then try with the backup code which was provided when you enable the Two Factor Authentication option.</p>

                <form method="POST" action="{{ route('verify.backup') }}">
                  @csrf
                  <div class="form-label-group">
                    <input id="two_factor_code" type="number" class="form-control @error('two_factor_code') is-invalid @enderror" name="two_factor_code" value="{{ old('two_factor_code') }}" placeholder="{{ __('locale.auth.two_factor_code') }}" required autocomplete="two_factor_code" autofocus>

                    <label for="two_factor_code">{{ __('locale.auth.two_factor_code') }}</label>

                    @error('two_factor_code')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>


                  <div class="float-md-left d-block mb-1">
                    <a href="{{ route('verify.index')}}" class="btn btn-outline-primary btn-block px-75">{{ __('locale.auth.verify') }}</a>
                  </div>

                  <div class="float-md-right d-block mb-1">
                    <button type="submit" class="btn btn-primary btn-block px-75">{{ __('locale.auth.verify_with_backup_code') }}</button>
                  </div>
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
