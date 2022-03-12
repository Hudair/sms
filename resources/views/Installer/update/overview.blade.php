@extends('layouts/fullLayoutMaster')

@section('title', 'Ultimate SMS Update')

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
                <div class="width-800 mx-auto">
                    <p class="paragraph text-center">{{ trans_choice('installer_messages.updater.overview.message', $numberOfUpdatesPending, ['number' => $numberOfUpdatesPending]) }}</p>
                    <div class="buttons">
                        <a href="{{ route('Updater::database') }}" class="button">{{ trans('installer_messages.updater.overview.install_updates') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
