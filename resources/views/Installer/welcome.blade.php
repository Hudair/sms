@extends('layouts/fullLayoutMaster')

@section('title', 'Ultimate SMS Auto Installer')


@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/wizard/bs-stepper.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->

    <link rel="stylesheet" href="{{ asset(mix('css/pages/authentication.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/bs-wizard.css')) }}">

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

    <!-- Vertical Wizard -->
    <section class="vertical-wizard flexbox-container">
        <div class="col-xl-12 col-12 d-flex justify-content-center">
            <div class="rounded-0 mb-0">
                <div class="bs-stepper vertical vertical-wizard-example">
                    <div class="bs-stepper-header">

                        <div class="step" data-target="#system_configuration">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box"><i class="feather icon-server"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title text-uppercase">System Compatibility</span>
                                    <span class="bs-stepper-subtitle">Check Environments</span>
                                </span>
                            </button>
                        </div>

                        <div class="step" data-target="#check-permissions">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box"><i class="feather icon-shield-off"></i> </span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title text-uppercase">Permissions</span>
                                    <span class="bs-stepper-subtitle">Set Folder Permissions</span>
                                </span>
                            </button>
                        </div>

                        <div class="step" data-target="#environment-settings">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box"><i class="feather icon-database"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title text-uppercase">Environment Settings</span>
                                    <span class="bs-stepper-subtitle">Update your settings</span>
                                </span>
                            </button>
                        </div>

                        <div class="step" data-target="#profile-settings">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box"><i class="feather icon-user"></i> </span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title text-uppercase">Profile Settings</span>
                                    <span class="bs-stepper-subtitle">Update Your Profile</span>
                                </span>
                            </button>
                        </div>
                    </div>


                    <div class="bs-stepper-content">
                        {{--System campability--}}
                        <div id="system_configuration" class="content">
                            <div class="content-header">
                                <h5 class="mb-0 text-primary text-uppercase">Minimum Server Requirements</h5>
                            </div>
                            <div class="row">

                                <div class="table-responsive">
                                    <table class="table">
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
                                                        <div class="badge badge-{{ $phpSupportInfo['supported'] ? 'success' : 'danger' }} text-uppercase mr-1 mb-1"><span>{{ $phpSupportInfo['current'] }}</span></div>
                                                    </td>
                                                </tr>
                                            @endif

                                            @foreach($requirements['requirements'][$type] as $extention => $enabled)
                                                <tr>
                                                    <td>{{ ucfirst($extention) }} PHP Extension</td>
                                                    <td>
                                                        @if($enabled)
                                                            <div class="badge badge-success text-uppercase mr-1 mb-1">
                                                                Enabled
                                                            </div>
                                                        @else

                                                            <div class="badge badge-danger text-uppercase mr-1 mb-1">
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

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-secondary btn-prev" disabled>
                                    <i class="align-middle mr-sm-25 mr-0 feather icon-arrow-left"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>


                                @if ( ! isset($requirements['errors']) && $phpSupportInfo['supported'] )
                                    <button class="btn btn-primary btn-next" data-id="is_valid">
                                        <span class="align-middle d-sm-inline-block text-white d-none">
                                            Next
                                        </span>
                                        <i class="feather icon-arrow-right align-middle ml-sm-25 ml-0"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{--check permissions--}}
                        <div id="check-permissions" class="content">
                            <div class="content-header">
                                <h5 class="mb-0 text-primary text-uppercase">Set Permission 775 following folders</h5>
                            </div>
                            <div class="row">

                                <div class="table-responsive">
                                    <table class="table">
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
                                                    <div class="badge badge-{{ $permission['isSet'] ? 'success' : 'danger' }} text-uppercase mr-1 mb-1">
                                                        <span>{{ $permission['permission'] }}</span>
                                                    </div>
                                                </td>
                                            </tr>

                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary btn-prev">
                                    <i class="align-middle mr-sm-25 mr-0 feather icon-arrow-left"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                @if ( ! isset($permissions['errors']))

                                    <button class="btn btn-primary btn-next" data-id="is_valid">
                                        <span class="align-middle d-sm-inline-block d-none">Next</span>
                                        <i class="align-middle ml-sm-25 ml-0 feather icon-arrow-right"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{--Environment settings--}}
                        <div id="environment-settings" class="content">
                            <form id="environment_form" style="width: 500px">
                                @csrf
                                <div class="content-header">
                                    <h5 class="mb-0">Environment Settings</h5>
                                    <small>Enter Your Database & Application Info.</small>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="form-label required" for="app_name">Application Name</label>
                                        <input type="text" id="app_name" class="form-control" name="app_name" required value="{{ config('app.name') }}"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="form-label required" for="app_url">HTTPS Enable</label>
                                        <select name="https_enable" id="https_enable" class="form-control" required>
                                            <option value="true" selected>Yes</option>
                                            <option value="false">No</option>
                                        </select>
                                    </div>


                                    <div class="form-group col-md-8">
                                        <label class="form-label required" for="app_url">Application URL</label>
                                        <input type="text" id="app_url" class="form-control" name="app_url" value="{{ rtrim(request()->url(), 'install') }}" required>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="form-label required" for="database-connection">Database Connection</label>
                                        <select name="database_connection" id="database-connection" class="form-control" required>
                                            <option value="mysql" selected>MySQL</option>
                                            <option value="sqlite">Sqlite</option>
                                            <option value="pgsql">PgSQL</option>
                                            <option value="sqlsrv">SQLSrv</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group col-md-8">
                                        <label class="form-label required" for="database_host">Database Host</label>
                                        <input type="text" id="database_host" class="form-control" value="127.0.0.1" name="database_host" required/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label required" for="port">Database Port</label>
                                        <input type="number" id="port" class="form-control" value="3306" name="database_port" required/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-8">
                                        <label class="form-label required" for="database_name">Database Name</label>
                                        <input type="text" id="database_name" class="form-control" name="database_name" required/>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label" for="database_prefix">Database Prefix</label>
                                        <input type="text" id="database_prefix" class="form-control" value="cg_" name="database_prefix"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label required" for="database_user_name">Database Username</label>
                                        <input type="text" id="database_user_name" class="form-control" name="database_username"/>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label required" for="database_password">Database Password</label>
                                        <input type="password" id="database_password" class="form-control" name="database_password"/>
                                    </div>

                                </div>

                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev">
                                        <i class="align-middle mr-sm-25 mr-0 feather icon-arrow-left"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>


                                    <button type="submit" class="btn btn-primary btn-save">
                                        <span class="align-middle d-sm-inline-block d-none">Save</span>
                                        <i class="align-middle ml-sm-25 ml-0 feather icon-arrow-right"></i>
                                    </button>
                                </div>


                            </form>
                        </div>

                        <div id="profile-settings" class="content">
                            <form id="profile_form">
                                @csrf
                                <div class="content-header">
                                    <h5 class="mb-0">Update Your Profile Information</h5>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label required" for="first_name">First Name</label>
                                        <input type="text" id="first_name" class="form-control" required name="first_name"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="last_name">Last Name</label>
                                        <input type="text" id="last_name" class="form-control" name="last_name"/>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group col-12">
                                        <label class="form-label required" for="email">Email Address</label>
                                        <input type="email" id="email" class="form-control" name="email" required/>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="form-group col-12">
                                        <label class="form-label required" for="password">Password</label>
                                        <input type="password" id="password" class="form-control" required name="password"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <label class="form-label required" for="admin_path">Admin Portal Path</label>
                                        <input type="text" id="admin_path" class="form-control" value="admin" required name="admin_path"/>
                                    </div>
                                    <p class="small text-primary">It's your admin portal access path url. It only contain one word like admin or admincp</p>
                                </div>


                                <div class="row">
                                    <div class="form-group col-12">
                                        <label class="form-label required" for="timezone">Timezone</label>
                                        <select class="form-control select2" id="timezone" name="timezone">
                                            @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                <option value="{{$timezone['zone']}}" {{ config('app.timezone') == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="customer" class="required">Create Customer</label>
                                            <select class="form-control" name="customer" id="customer">
                                                <option value="1">{{ __('locale.labels.active') }}</option>
                                                <option value="0">{{ __('locale.labels.inactive')}} </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev">
                                        <i class="align-middle mr-sm-25 mr-0 feather icon-arrow-left"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>


                                    <button type="submit" class="btn btn-primary btn-save">
                                        <span class="align-middle d-sm-inline-block d-none">Save</span>
                                        <i class="align-middle ml-sm-25 ml-0 feather icon-save"></i>
                                    </button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Vertical Wizard -->

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/wizard/bs-stepper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script>
        $(function () {
            'use strict';

            let bsStepper = document.querySelectorAll('.bs-stepper'),
                select = $('.select2'),
                verticalWizard = document.querySelector('.vertical-wizard-example');

            select.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
            });


            // Adds crossed class
            if (typeof bsStepper !== undefined && bsStepper !== null) {
                for (let el = 0; el < bsStepper.length; ++el) {
                    bsStepper[el].addEventListener('show.bs-stepper', function (event) {
                        let index = event.detail.indexStep;
                        let numberOfSteps = $(event.target).find('.step').length - 1;
                        let line = $(event.target).find('.step');

                        // The first for loop is for increasing the steps,
                        // the second is for turning them off when going back
                        // and the third with the if statement because the last line
                        // can't seem to turn off when I press the first item. ¯\_(ツ)_/¯

                        for (let i = 0; i < index; i++) {
                            line[i].classList.add('crossed');

                            for (let j = index; j < numberOfSteps; j++) {
                                line[j].classList.remove('crossed');
                            }
                        }
                        if (event.detail.to === 0) {
                            for (let k = index; k < numberOfSteps; k++) {
                                line[k].classList.remove('crossed');
                            }
                            line[0].classList.remove('crossed');
                        }
                    });
                }
            }


            // Vertical Wizard
            // --------------------------------------------------------------------
            if (typeof verticalWizard !== undefined && verticalWizard !== null) {

                let numberedStepper = new Stepper(verticalWizard);

                let verticalStepper = new Stepper(verticalWizard, {
                    linear: false,
                });

                // verticalStepper.to(4);


                $(verticalWizard)
                    .find('.btn-next')
                    .each(function () {
                        $(this).on('click', function (e) {
                            let isValid = $(this).data('id');
                            if (isValid === 'is_valid') {
                                numberedStepper.next();
                            } else {
                                e.preventDefault();
                            }
                        });
                    });

                $(verticalWizard)
                    .find('.btn-next')
                    .each(function () {
                        $(this).on('click', function (e) {
                            let isValid = $(this).data('id');
                            if (isValid === 'is_valid') {
                                numberedStepper.next();
                            } else {
                                e.preventDefault();
                            }
                        });
                    });

                $('#environment_form').on('submit', function (e) {
                    e.preventDefault();

                    $.ajax({
                        type: 'post',
                        url: "{{ route('Installer::environmentDatabase') }}",
                        data: $('#environment_form').serialize(),
                        success: function (data) {

                            if (data.status === 'success') {
                                toastr.success(data.message, 'Success!!', {
                                    positionClass: 'toast-top-right',
                                    containerId: 'toast-top-right',
                                    progressBar: true,
                                    closeButton: true,
                                    newestOnTop: true
                                });

                                numberedStepper.next();
                            } else {

                                $.each(data.message, function (key, value) {
                                    toastr.error(value[0], "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                });
                            }

                        }
                    })
                });

                $('#profile_form').on('submit', function (e) {
                    e.preventDefault();

                    toastr.success('It will take few minutes. Please don\'t reload the page.', 'Success!!', {
                        positionClass: 'toast-top-right',
                        containerId: 'toast-top-right',
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });

                    $(".btn-save").attr("disabled", true);

                    $.ajax({
                        type: 'post',
                        url: "{{ route('Installer::database') }}",
                        data: $('#profile_form').serialize(),
                        success: function (data) {

                            if (data.status === 'success') {
                                toastr.success(data.message, 'Success!!', {
                                    positionClass: 'toast-top-right',
                                    containerId: 'toast-top-right',
                                    progressBar: true,
                                    closeButton: true,
                                    newestOnTop: true
                                });

                                setTimeout(function () {
                                    window.location = data.response_url;
                                }, 2000);
                            } else {

                                $.each(data.message, function (key, value) {
                                    toastr.error(value[0], "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                });
                            }

                        },

                        error: function (reject) {
                            $(".btn-save").attr("disabled", false);

                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                });
                            } else {
                                toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                    positionClass: 'toast-top-right',
                                    containerId: 'toast-top-right',
                                    progressBar: true,
                                    closeButton: true,
                                    newestOnTop: true
                                });
                            }
                        }
                    })
                });


                $(verticalWizard)
                    .find('.btn-next')
                    .on('click', function () {
                        verticalStepper.next();
                    });
                $(verticalWizard)
                    .find('.btn-prev')
                    .on('click', function () {
                        verticalStepper.previous();
                    });
            }

        });
    </script>
@endsection
