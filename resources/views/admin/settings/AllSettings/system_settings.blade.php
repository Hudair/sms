@extends('layouts.contentLayoutMaster')

@section('title',__('locale.menu.All Settings'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
@endsection


@section('content')


    <section id="nav-justified">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified mt-5" id="myTab2" role="tablist">

                                {{-- Gerenal --}}
                                @can('general settings')
                                    <li class="nav-item">
                                        <a class="nav-link @if (old('tab') == 'general' || old('tab') == null) active @endif" id="general-tab-justified" data-bs-toggle="tab" href="#general" role="tab"
                                           aria-controls="general" aria-selected="true"><i data-feather="settings" class="primary"></i> {{ __('locale.labels.general') }}</a>
                                    </li>
                                @endcan

                                {{-- system email --}}
                                @can('system_email settings')
                                    <li class="nav-item">
                                        <a class="nav-link {{ old('tab') == 'system_email' ? 'active':null }}" id="system-email-tab-justified" data-bs-toggle="tab" href="#system-email" role="tab"
                                           aria-controls="system-email" aria-selected="true"><i data-feather="mail" class="primary"></i> {{ __('locale.labels.system_email') }}</a>
                                    </li>
                                @endcan

                                {{-- authentication --}}
                                @can('authentication settings')
                                    <li class="nav-item">
                                        <a class="nav-link {{ old('tab') == 'authentication' ? 'active':null }}" id="authentication-tab-justified" data-bs-toggle="tab" href="#authentication" role="tab"
                                           aria-controls="authentication" aria-selected="true"><i data-feather="lock" class="primary"></i> {{ __('locale.labels.authentication') }}</a>
                                    </li>
                                @endcan

                                {{-- notifications --}}
                                @can('notifications settings')
                                    <li class="nav-item">
                                        <a class="nav-link {{ old('tab') == 'notifications' ? 'active':null }}" id="notifications-tab-justified" data-bs-toggle="tab" href="#notifications" role="tab"
                                           aria-controls="notifications" aria-selected="true"><i data-feather="bell" class="primary"></i> {{ __('locale.labels.notifications') }}</a>
                                    </li>
                                @endcan

                                {{-- pusher --}}
                                @can('pusher settings')
                                    <li class="nav-item">
                                        <a class="nav-link {{ old('tab') == 'pusher' ? 'active':null }}" id="pusher-tab-justified" data-bs-toggle="tab" href="#pusher" role="tab"
                                           aria-controls="pusher" aria-selected="true"><i data-feather="message-square" class="primary"></i> {{ __('locale.labels.pusher') }}</a>
                                    </li>
                                @endcan

                                {{-- Background job --}}
                                @can('view background_jobs')
                                    <li class="nav-item">
                                        <a class="nav-link {{ old('tab') == 'cron_job' ? 'active':null }}" id="cron-job-tab-justified" data-bs-toggle="tab" href="#cron-job" role="tab"
                                           aria-controls="cron-job" aria-selected="true"><i data-feather="clock" class="primary"></i> {{ __('locale.labels.cron_job') }}</a>
                                    </li>
                                @endcan

                                {{-- License --}}
                                @if(config('app.env') != 'demo')
                                    @can('view purchase_code')
                                        <li class="nav-item {{ old('tab') == 'license' ? 'active':null }}">
                                            <a class="nav-link" id="license-tab-justified" data-bs-toggle="tab" href="#license" role="tab"
                                               aria-controls="license" aria-selected="true"><i data-feather="file-text" class="primary"></i> {{ __('locale.labels.license') }}</a>
                                        </li>
                                    @endcan
                                @endif


                            </ul>


                            {{-- Tab panes --}}
                            <div class="tab-content pt-1">


                                {{-- Gerenal --}}
                                @can('general settings')
                                    <div class="tab-pane @if (old('tab') == 'general' || old('tab') == null) active @endif" id="general" role="tabpanel" aria-labelledby="general-tab-justified">
                                        @include('admin.settings.AllSettings._general')
                                    </div>
                                @endcan


                                {{-- system email --}}
                                @can('system_email settings')
                                    <div class="tab-pane {{ old('tab') == 'system_email' ? 'active':null }}" id="system-email" role="tabpanel" aria-labelledby="system-email-tab-justified">
                                        @include('admin.settings.AllSettings._system_email')
                                    </div>
                                @endcan

                                {{-- authentication --}}
                                @can('authentication settings')
                                    <div class="tab-pane {{ old('tab') == 'authentication' ? 'active':null }}" id="authentication" role="tabpanel" aria-labelledby="authentication-tab-justified">
                                        @include('admin.settings.AllSettings._authentication')
                                    </div>
                                @endcan

                                {{-- notifications --}}
                                @can('notifications settings')
                                    <div class="tab-pane {{ old('tab') == 'notifications' ? 'active':null }}" id="notifications" role="tabpanel" aria-labelledby="notifications-tab-justified">
                                        @include('admin.settings.AllSettings._notifications')
                                    </div>
                                @endcan

                                {{-- pusher --}}
                                @can('pusher settings')
                                    <div class="tab-pane {{ old('tab') == 'pusher' ? 'active':null }}" id="pusher" role="tabpanel" aria-labelledby="pusher-tab-justified">
                                        @include('admin.settings.AllSettings._pusher')
                                    </div>
                                @endcan

                                {{-- Background job --}}
                                @can('view background_jobs')
                                    <div class="tab-pane {{ old('tab') == 'cron_tab' ? 'active':null }}" id="cron-job" role="tabpanel" aria-labelledby="cron-job-tab-justified">
                                        @include('admin.settings.AllSettings._background_jobs')
                                    </div>
                                @endcan

                                @if(config('app.env') != 'demo')
                                    {{-- License --}}
                                    @can('view purchase_code')
                                        <div class="tab-pane {{ old('tab') == 'license' ? 'active':null }}" id="license" role="tabpanel" aria-labelledby="license-tab-justified">
                                            @include('admin.settings.AllSettings._license')
                                        </div>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        $(document).ready(function () {
            // Basic Select2 select
            $(".select2").each(function () {
                let $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    // the following code is used to disable x-scrollbar when click in select input and
                    // take 100% width in responsive also
                    dropdownAutoWidth: true,
                    width: '100%',
                    dropdownParent: $this.parent()
                });
            });


            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            let EmailGatewaySV = $('.gateway');

            if (EmailGatewaySV.val() === 'sendmail') {
                $('.show-smtp').hide();
            }

            EmailGatewaySV.on('change', function () {

                let value = $(this).val();
                if (value === 'smtp') {
                    $('.show-smtp').show();
                } else {
                    $('.show-smtp').hide();
                }
            });


            let ShowTwoFactor = $('#two_factor');

            if (ShowTwoFactor.val() === '0') {
                $('.show-two-factor').hide();
            }

            ShowTwoFactor.on('change', function () {

                let value = $(this).val();
                if (value === '1') {
                    $('.show-two-factor').show();
                } else {
                    $('.show-two-factor').hide();
                }
            });

            let LoginWithFacebook = $('#login_with_facebook');

            if (LoginWithFacebook.val() === '0') {
                $('.show-facebook').hide();
            }

            LoginWithFacebook.on('change', function () {

                let value = $(this).val();
                if (value === '1') {
                    $('.show-facebook').show();
                } else {
                    $('.show-facebook').hide();
                }
            });


            let LoginWithTwitter = $('#login_with_twitter');

            if (LoginWithTwitter.val() === '0') {
                $('.show-twitter').hide();
            }

            LoginWithTwitter.on('change', function () {

                let value = $(this).val();
                if (value === '1') {
                    $('.show-twitter').show();
                } else {
                    $('.show-twitter').hide();
                }
            });


            let LoginWithGoogle = $('#login_with_google');

            if (LoginWithGoogle.val() === '0') {
                $('.show-google').hide();
            }

            LoginWithGoogle.on('change', function () {

                let value = $(this).val();
                if (value === '1') {
                    $('.show-google').show();
                } else {
                    $('.show-google').hide();
                }
            });


            let LoginWithGitHub = $('#login_with_github');

            if (LoginWithGitHub.val() === '0') {
                $('.show-github').hide();
            }

            LoginWithGitHub.on('change', function () {

                let value = $(this).val();
                if (value === '1') {
                    $('.show-github').show();
                } else {
                    $('.show-github').hide();
                }
            });


            $('input[name="php_bin_path"]:checked').trigger('change');

            // pickadate mask
            $(document).on('keyup change', 'input[name="php_bin_path"]', function () {
                let value = $(this).val();

                if (value !== '') {
                    $('.current_path_value').html(value);
                } else {
                    $('.current_path_value').html('{PHP_BIN_PATH}');
                }
            });
            $('input[name="php_bin_path_value"]').trigger('change');


        });
    </script>
@endsection
