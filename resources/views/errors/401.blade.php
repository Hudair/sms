@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', __('locale.http.401.title'))
@section('code', '401')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-misc.css')) }}">
@endsection
@section('content')
    <!-- Error page-->
    <div class="misc-wrapper">

        <a class="brand-logo" href="{{route('login')}}">
            <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}"/>
        </a>

        <div class="misc-inner p-2 p-sm-3">
            <div class="w-100 text-center">
                <h2 class="mb-1">{{__('locale.http.401.title')}}!️</h2>
                <p class="mb-2">{{ __($exception->getMessage() ?: __('locale.http.401.description')) }}</p>
                <a class="btn btn-primary mb-2 btn-sm-block" href="{{ route('login') }}">{{__('locale.labels.back_to_home')}}</a>
                @if($configData['theme'] === 'dark')
                    <img class="img-fluid" src="{{asset('images/pages/error-dark.svg')}}" alt="Error page" />
                @else
                    <img class="img-fluid" src="{{asset('images/pages/error.svg')}}" alt="Error page" />
                @endif
            </div>
        </div>
    </div>
    <!-- / Error page-->
@endsection
