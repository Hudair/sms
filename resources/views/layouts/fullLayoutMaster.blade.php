@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
<html lang="@if(session()->has('locale')){{Session::get('locale')}}@else{{"en"}}@endif" data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="keywords" content="{{config('app.keyword')}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{config('app.title')}}</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo asset(config('app.favicon')); ?>"/>

    {{-- Include core + vendor Styles --}}
    @include('panels/styles')

</head>

{{-- {!! Helper::applClasses() !!} --}}
@php
$configData = Helper::applClasses();
@endphp

<body
    class="vertical-layout vertical-menu-modern 1-column {{ $configData['blankPageClass']}} {{ $configData['bodyClass']}} {{($configData['theme'] === 'light') ? '' : $configData['layoutTheme'] }} {{ $configData['footerType'] }}"
    data-menu="vertical-menu-modern" data-col="1-column">

    <!-- BEGIN: Header-->
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-wrapper">

            <div class="content-body">
                {{-- Include Page Content --}}
                @yield('content')
            </div>
        </div>
    </div>
    <!-- End: Content-->

    {{-- include default scripts --}}
    @include('panels/scripts')
    @stack('scripts')

</body>

</html>
