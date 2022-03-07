@extends('layouts.contentLayoutMaster')

@section('title',__('locale.menu.All Settings'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/noui-slider.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-noui.css')) }}">
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
                                        <a class="nav-link active" id="general-tab-justified" data-toggle="tab" href="#general-just" role="tab"
                                           aria-controls="general-just" aria-selected="true"><i class="feather icon-settings primary"></i> {{ __('locale.labels.general') }}</a>
                                    </li>
                                @endcan

                                {{-- system email --}}
                                @can('system_email settings')
                                    <li class="nav-item">
                                        <a class="nav-link" id="system-email-tab-justified" data-toggle="tab" href="#system-email-just" role="tab"
                                           aria-controls="system-email-just" aria-selected="true"><i class="feather icon-mail primary"></i> {{ __('locale.labels.system_email') }}</a>
                                    </li>
                                @endcan

                                {{-- authentication --}}
                                @can('authentication settings')
                                    <li class="nav-item">
                                        <a class="nav-link" id="authentication-tab-justified" data-toggle="tab" href="#authentication-just" role="tab"
                                           aria-controls="authentication-just" aria-selected="true"><i class="feather icon-lock primary"></i> {{ __('locale.labels.authentication') }}</a>
                                    </li>
                                @endcan

                                {{-- notifications --}}
                                @can('notifications settings')
                                    <li class="nav-item">
                                        <a class="nav-link" id="notifications-tab-justified" data-toggle="tab" href="#notifications-just" role="tab"
                                           aria-controls="notifications-just" aria-selected="true"><i class="feather icon-bell primary"></i> {{ __('locale.labels.notifications') }}</a>
                                    </li>
                                @endcan

                                {{-- pusher --}}
                                @can('pusher settings')
                                    <li class="nav-item">
                                        <a class="nav-link" id="pusher-tab-justified" data-toggle="tab" href="#pusher-just" role="tab"
                                           aria-controls="pusher-just" aria-selected="true"><i class="feather icon-message-square primary"></i> {{ __('locale.labels.pusher') }}</a>
                                    </li>
                                @endcan

                                {{-- Background job --}}
                                @can('view background_jobs')
                                    <li class="nav-item">
                                        <a class="nav-link" id="cron-job-tab-justified" data-toggle="tab" href="#cron-job-just" role="tab"
                                           aria-controls="cron-job-just" aria-selected="true"><i class="feather icon-clock primary"></i> {{ __('locale.labels.cron_job') }}</a>
                                    </li>
                                @endcan

                                {{-- License --}}

                                @if(config('app.env') != 'demo')
                                    @can('view purchase_code')
                                        <li class="nav-item">
                                            <a class="nav-link" id="license-tab-justified" data-toggle="tab" href="#license-just" role="tab"
                                               aria-controls="license-just" aria-selected="true"><i class="feather icon-file-text primary"></i> {{ __('locale.labels.license') }}</a>
                                        </li>
                                    @endcan
                                @endif


                            </ul>


                            {{-- Tab panes --}}
                            <div class="tab-content pt-1">


                                {{-- Gerenal --}}
                                @can('general settings')
                                    <div class="tab-pane active" id="general-just" role="tabpanel" aria-labelledby="general-tab-justified">
                                        @include('admin.settings.AllSettings._general')
                                    </div>
                                @endcan


                                {{-- system email --}}
                                @can('system_email settings')
                                    <div class="tab-pane" id="system-email-just" role="tabpanel" aria-labelledby="system-email-tab-justified">
                                        @include('admin.settings.AllSettings._system_email')
                                    </div>
                                @endcan

                                {{-- authentication --}}
                                @can('authentication settings')
                                    <div class="tab-pane" id="authentication-just" role="tabpanel" aria-labelledby="authentication-tab-justified">
                                        @include('admin.settings.AllSettings._authentication')
                                    </div>
                                @endcan

                                {{-- notifications --}}
                                @can('notifications settings')
                                    <div class="tab-pane" id="notifications-just" role="tabpanel" aria-labelledby="notifications-tab-justified">
                                        @include('admin.settings.AllSettings._notifications')
                                    </div>
                                @endcan

                                {{-- pusher --}}
                                @can('pusher settings')
                                    <div class="tab-pane" id="pusher-just" role="tabpanel" aria-labelledby="pusher-tab-justified">
                                        @include('admin.settings.AllSettings._pusher')
                                    </div>
                                @endcan

                                {{-- Background job --}}
                                @can('view background_jobs')
                                    <div class="tab-pane" id="cron-job-just" role="tabpanel" aria-labelledby="cron-job-tab-justified">
                                        @include('admin.settings.AllSettings._background_jobs')
                                    </div>
                                @endcan

                                @if(config('app.env') != 'demo')
                                    {{-- License --}}
                                    @can('view purchase_code')
                                        <div class="tab-pane" id="license-just" role="tabpanel" aria-labelledby="license-tab-justified">
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
    <script src="{{ asset(mix('vendors/js/extensions/wNumb.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/nouislider.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        $(document).ready(function () {

            //show response message
            function showResponseMessage(data) {

                if (data.status === 'success') {
                    toastr.success(data.message, 'Success!!', {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                    setTimeout(function () {
                        window.location.reload();
                    }, 5000);
                } else {
                    toastr.warning("{{__('locale.exceptions.something_went_wrong')}}", "{{__('locale.labels.attention')}}", {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                }
            }

            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
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
