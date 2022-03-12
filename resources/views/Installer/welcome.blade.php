@extends('layouts/fullLayoutMaster')

@section('title', 'Ultimate SMS Auto Installer')


@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-wizard.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">


    <style>

        table {
            width: 100%;
            padding: 10px;
            border-radius: 3px;
        }

        table thead th {
            text-align: left;
            padding: 5px 0 5px 0;
        }

        table tbody td {
            padding: 5px 0;
        }

        table tbody td:last-child, table thead th:last-child {
            text-align: right;
        }
    </style>
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
            <div class="col-lg-3 d-none d-lg-flex align-items-center p-0">
                <div class="w-100 d-lg-flex align-items-center justify-content-center">
                    <img class="img-fluid w-100" src="{{asset('images/pages/create-account.svg')}}" alt="{{config('app.name')}}"/>
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Register-->
            <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                <div class="width-1200 mx-auto">
                    <div class="bs-stepper register-multi-steps-wizard shadow-none">
                        <div class="bs-stepper-header px-0" role="tablist">

                            <div class="step" data-target="#system_configuration" role="tab" id="system_configuration-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                      <i data-feather="server" class="font-medium-3"></i>
                                    </span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">System Compatibility</span>
                                        <span class="bs-stepper-subtitle">Check Environments</span>
                                    </span>
                                </button>
                            </div>


                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>

                            <div class="step" data-target="#check-permissions" role="tab" id="check-permissions-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                        <i data-feather="shield-off" class="font-medium-3"></i>
                                    </span>

                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Permissions</span>
                                        <span class="bs-stepper-subtitle">Set Folder Permissions</span>
                                    </span>
                                </button>
                            </div>


                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>

                            <div class="step" data-target="#environment-settings" role="tab" id="environment-settings-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                      <i data-feather="database" class="font-medium-3"></i>
                                    </span>

                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Environment Settings</span>
                                        <span class="bs-stepper-subtitle">Update your settings</span>
                                    </span>
                                </button>
                            </div>

                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>

                            <div class="step" data-target="#profile-settings" role="tab" id="profile-settings-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">
                                      <i data-feather="user" class="font-medium-3"></i>
                                    </span>

                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Profile Settings</span>
                                        <span class="bs-stepper-subtitle">Update your profile</span>
                                    </span>
                                </button>
                            </div>

                        </div>

                        <div class="bs-stepper-content px-0 mt-4">

                            @if ($errors->any())

                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger" role="alert">
                                        <div class="alert-body">{{ $error }}</div>
                                    </div>
                                @endforeach

                            @endif


                            <div id="system_configuration" class="content get_form_data" role="tabpanel" aria-labelledby="system_configuration-trigger">
                                <div class="content-header mb-2">
                                    <h5 class="fw-bolder mb-75">System Compatibility</h5>
                                    <span>Check Environments</span>
                                </div>

                                <div class="row">

                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <thead>
                                            <tr>
                                                <th style="width: 500px">Requirements</th>
                                                <th>Result</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($requirements['requirements'] as $type => $requirement)

                                                @if($type == 'php')
                                                    <tr>
                                                        <td>PHP {{ $phpSupportInfo['minimum'] }} </td>

                                                        <td>
                                                            <div class="badge bg-{{ $phpSupportInfo['supported'] ? 'success' : 'danger' }} text-uppercase mr-1 mb-1"><span>{{ $phpSupportInfo['current'] }}</span></div>
                                                        </td>
                                                    </tr>
                                                @endif

                                                @foreach($requirements['requirements'][$type] as $extention => $enabled)
                                                    <tr>
                                                        <td>{{ ucfirst($extention) }} PHP Extension</td>
                                                        <td>
                                                            @if($enabled)
                                                                <div class="badge bg-success text-uppercase mr-1 mb-1">
                                                                    Enabled
                                                                </div>
                                                            @else

                                                                <div class="badge bg-danger text-uppercase mr-1 mb-1">
                                                                    Not Enabled
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach


                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <button class="btn btn-outline-secondary btn-prev" disabled type="button">
                                        <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                    </button>

                                    @if ( ! isset($requirements['errors']) && $phpSupportInfo['supported'] )
                                        <button class="btn btn-primary btn-next" type="button" data-id="is_valid">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                            <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>


                            <div id="check-permissions" class="content get_form_data" role="tabpanel" aria-labelledby="check-permissions-trigger">
                                <div class="content-header mb-2">
                                    <h5 class="fw-bolder mb-75">Check Permissions</h5>
                                    <span>Set Permission 775 following folders</span>
                                </div>

                                <div class="row">

                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <thead>
                                            <tr>
                                                <th>Folder</th>
                                                <th>Permission</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($permissions['permissions'] as $permission)
                                                <tr>
                                                    <td>{{ $permission['folder'] }} </td>

                                                    <td>
                                                        <div class="badge bg-{{ $permission['isSet'] ? 'success' : 'danger' }} text-uppercase mr-1 mb-1">
                                                            <span>{{ $permission['permission'] }}</span>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <button class="btn btn-primary btn-prev" type="button">
                                        <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                    </button>

                                    @if ( ! isset($permissions['errors']))
                                        <button class="btn btn-primary btn-next" type="button" data-id="is_valid">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.next') }}</span>
                                            <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div id="environment-settings" class="content get_form_data" role="tabpanel" aria-labelledby="environment-settings-trigger">
                                <form id="environment_form">
                                    @csrf
                                    <div class="content-header mb-2">
                                        <h5 class="fw-bolder mb-75">Environment Settings</h5>
                                        <span>Enter Your Database & Application Info.</span>
                                    </div>

                                    <div class="row">
                                        <div class="mb-1 col-md-12">
                                            <label class="form-label required" for="app_name">Application Name</label>
                                            <input type="text" id="app_name" class="form-control" name="app_name" required value="{{ config('app.name') }}"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-1 col-md-4">
                                            <label class="form-label required" for="app_url">HTTPS Enable</label>
                                            <select name="https_enable" id="https_enable" class="form-select" required>
                                                <option value="true" selected>Yes</option>
                                                <option value="false">No</option>
                                            </select>
                                        </div>


                                        <div class="mb-1 col-md-8">
                                            <label class="form-label required" for="app_url">Application URL</label>
                                            <input type="text" id="app_url" class="form-control" name="app_url" value="{{ rtrim(request()->url(), 'install') }}" required>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="mb-1 col-md-12">
                                            <label class="form-label required" for="database-connection">Database Connection</label>
                                            <select name="database_connection" id="database-connection" class="form-select" required>
                                                <option value="mysql" selected>MySQL</option>
                                                <option value="sqlite">Sqlite</option>
                                                <option value="pgsql">PgSQL</option>
                                                <option value="sqlsrv">SQLSrv</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="mb-1 col-md-8">
                                            <label class="form-label required" for="database_host">Database Host</label>
                                            <input type="text" id="database_host" class="form-control" value="127.0.0.1" name="database_host" required/>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <label class="form-label required" for="port">Database Port</label>
                                            <input type="number" id="port" class="form-control" value="3306" name="database_port" required/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-1 col-md-8">
                                            <label class="form-label required" for="database_name">Database Name</label>
                                            <input type="text" id="database_name" class="form-control" name="database_name" required/>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <label class="form-label" for="database_prefix">Database Prefix</label>
                                            <input type="text" id="database_prefix" class="form-control" value="cg_" name="database_prefix"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="database_user_name">Database Username</label>
                                            <input type="text" id="database_user_name" class="form-control" name="database_username"/>
                                        </div>

                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="database_password">Database Password</label>
                                            <input type="password" id="database_password" class="form-control" name="database_password"/>
                                        </div>

                                    </div>


                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-primary btn-prev" type="button">
                                            <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                        </button>

                                        <button class="btn btn-primary btn-save" type="submit">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.buttons.save') }}</span>
                                            <i data-feather="save" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>

                                    </div>
                                </form>
                            </div>

                            <div id="profile-settings" class="content" role="tabpanel" aria-labelledby="profile-settings-trigger">
                                <form id="profile_form">
                                    @csrf
                                    <div class="content-header">
                                        <h5 class="fw-bolder mb-75">Update Your Profile Information</h5>
                                    </div>
                                    <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label required" for="first_name">First Name</label>
                                            <input type="text" id="first_name" class="form-control" required name="first_name"/>
                                        </div>
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="last_name">Last Name</label>
                                            <input type="text" id="last_name" class="form-control" name="last_name"/>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="mb-1 col-12">
                                            <label class="form-label required" for="email">Email Address</label>
                                            <input type="email" id="email" class="form-control" name="email" required/>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="mb-1 col-12">
                                            <label class="form-label required" for="password">Password</label>
                                            <input type="password" id="password" class="form-control" required name="password"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="mb-1 col-12">
                                            <label class="form-label required" for="admin_path">Admin Portal Path</label>
                                            <input type="text" id="admin_path" class="form-control" value="admin" required name="admin_path"/>
                                        </div>
                                        <p><small class="text-primary">It's your admin portal access path url. It only contains one word like admin or admincp</small></p>
                                    </div>


                                    <div class="row">
                                        <div class="mb-1 col-12">
                                            <label class="form-label required" for="timezone">Timezone</label>
                                            <select class="form-select select2" id="timezone" name="timezone">
                                                @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                    <option value="{{$timezone['zone']}}" {{ config('app.timezone') == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="customer" class="required">Create Customer</label>
                                                <select class="form-select" name="customer" id="customer">
                                                    <option value="1">{{ __('locale.labels.active') }}</option>
                                                    <option value="0">{{ __('locale.labels.inactive')}} </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-2">
                                        <button class="btn btn-primary btn-prev" type="button">
                                            <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.datatables.previous') }}</span>
                                        </button>

                                        <button class="btn btn-primary btn-save" type="submit">
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('locale.buttons.save') }}</span>
                                            <i data-feather="save" class="align-middle ms-sm-25 ms-0"></i>
                                        </button>

                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset(mix('vendors/js/forms/wizard/bs-stepper.min.js'))}}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        let registerMultiStepsWizard = document.querySelector('.register-multi-steps-wizard'),
            pageResetForm = $('.auth-register-form'),
            numberedStepper,
            select = $('.select2');


        // multi-steps registration
        // --------------------------------------------------------------------

        // Horizontal Wizard
        if (typeof registerMultiStepsWizard !== undefined && registerMultiStepsWizard !== null) {
            numberedStepper = new Stepper(registerMultiStepsWizard);

            $(registerMultiStepsWizard)
                .find('.btn-next')
                .each(function () {
                    $(this).on('click', function () {
                        numberedStepper.next();
                    });
                });

            $(registerMultiStepsWizard)
                .find('.btn-prev')
                .on('click', function () {
                    numberedStepper.previous();
                });
        }


        $('#environment_form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                type: 'post',
                url: "{{ route('Installer::environmentDatabase') }}",
                data: $('#environment_form').serialize(),
                success: function (data) {

                    if (data.status === 'success') {
                        toastr['success'](data.message, 'Success!!', {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });

                        numberedStepper.next();
                    } else {

                        $.each(data.message, function (key, value) {
                            toastr['error'](value[0], "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    }

                }
            })
        });

        $('#profile_form').on('submit', function (e) {
            e.preventDefault();

            toastr['success']('It will take few minutes. Please don\'t reload the page.', 'Success!!', {
                closeButton: true,
                positionClass: 'toast-top-right',
                progressBar: true,
                newestOnTop: true,
                rtl: isRtl
            });

            $(".btn-save").attr("disabled", true);

            $.ajax({
                type: 'post',
                url: "{{ route('Installer::database') }}",
                data: $('#profile_form').serialize(),
                success: function (data) {

                    if (data.status === 'success') {
                        toastr['success'](data.message, 'Success!!', {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });

                        setTimeout(function () {
                            window.location = data.response_url;
                        }, 2000);
                    } else {

                        $.each(data.message, function (key, value) {
                            toastr['error'](value[0], "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    }

                },

                error: function (reject) {
                    $(".btn-save").attr("disabled", false);

                    if (reject.status === 422) {
                        let errors = reject.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        });
                    } else {
                        toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                            rtl: isRtl
                        });
                    }
                }
            })
        });

        // select2
        select.each(function () {
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

    </script>
@endsection

