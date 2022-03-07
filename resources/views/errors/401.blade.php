@extends('layouts/fullLayoutMaster')

@section('title', __('locale.http.401.title'))
@section('code', '401')

@section('content')
    <!-- maintenance -->
    <section class="row flexbox-container">
        <div class="col-12 d-flex justify-content-center">
            <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                <div class="card-content">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/pages/not-authorized.png') }}" class="img-fluid align-self-center" alt="{{ config('app.name') }}">
                        <h1 class="font-large-2 my-1">{{__('locale.http.401.title')}}!</h1>
                        <p class="px-2">
                            {{ __($exception->getMessage() ?: __('locale.http.401.description')) }}
                        </p>
                        <a class="btn btn-primary btn-lg mt-1" href="{{ route('login') }}">{{__('locale.labels.back_to_home')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- maintenance end -->
@endsection
