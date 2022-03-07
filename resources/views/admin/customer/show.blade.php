@extends('layouts/contentLayoutMaster')

@section('title', $customer->displayName())

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/pages/app-user.css')) }}">

@endsection

@section('content')
    <!-- users edit start -->
    <section class="users-edit">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-justified mb-3" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                                <i class="feather icon-user mr-25"></i>{{__('locale.labels.account')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="information-tab" data-toggle="tab" href="#information" aria-controls="information" role="tab" aria-selected="false">
                                <i class="feather icon-info mr-25"></i>{{__('locale.labels.information')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="permission-tab" data-toggle="tab" href="#permission" aria-controls="permission" role="tab" aria-selected="false">
                                <i class="feather icon-lock mr-25"></i>{{__('locale.labels.permissions')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="usms_subscription-tab" data-toggle="tab" href="#usms_subscription" aria-controls="usms_subscription" role="tab" aria-selected="false">
                                <i class="feather icon-shopping-cart mr-25"></i>{{__('locale.menu.Subscriptions')}}
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content">

                        <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
                            <!-- users edit media object start -->
                            <div class="media mb-2">
                                <a class="mr-2 my-25" href="{{ route('admin.customers.show', $customer->uid) }}">
                                    <img src="{{ route('admin.customers.avatar', $customer->uid) }}" alt="{{ $customer->displayName() }}" class="users-avatar-shadow rounded" height="120" width="120">
                                </a>
                                <div class="media-body mt-50">
                                    <h4 class="media-heading">{{ $customer->displayName() }}</h4>
                                    <h5 class="media-heading"> {{__('locale.labels.sms_credit')}} : {{ $customer->sms_unit == '-1' ? __('locale.labels.unlimited') : $customer->sms_unit }}</h5>
                                    <div class="col-12 d-flex mt-1 px-0">
                                        @include('admin.customer._update_avatar')
                                        @if($customer->customer->activeSubscription())
                                            @include('admin.customer._add_unit')
                                        @endif
                                        <span id="remove-avatar" data-id="{{$customer->uid}}" class="btn btn-outline-danger d-none d-sm-block"><i class="feather icon-trash-2"></i> {{__('locale.labels.remove')}}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- users edit media object ends -->

                            <!-- users edit account form start -->
                        @include('admin.customer._account')
                        <!-- users edit account form ends -->

                        </div>

                        <div class="tab-pane" id="information" aria-labelledby="information-tab" role="tabpanel">
                            <!-- users edit Info form start -->
                        @include('admin.customer._information')
                        <!-- users edit Info form ends -->
                        </div>

                        <div class="tab-pane" id="permission" aria-labelledby="permission-tab" role="tabpanel">
                            <!-- user permission form start -->
                        @include('admin.customer._permissions')
                        <!-- user permission form end -->
                        </div>

                        <div class="tab-pane" id="usms_subscription" aria-labelledby="usms_subscription-tab" role="tabpanel">
                            @include('admin.customer._subscription')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- users edit ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/navs/navs.js')) }}"></script>

    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);
            let showHideInput = $('.show_hide_password input');
            let showHideIcon = $('.show_hide_password i');

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }


            // Basic Select2 select
            $(".select2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%'
            });


            $(".form-control-position").on('click', function (event) {
                event.preventDefault();
                if (showHideInput.attr("type") === "text") {
                    showHideInput.attr('type', 'password');
                    showHideIcon.addClass("icon-eye-off");
                    showHideIcon.removeClass("icon-eye");
                } else if (showHideInput.attr("type") === "password") {
                    showHideInput.attr('type', 'text');
                    showHideIcon.removeClass("icon-eye-off");
                    showHideIcon.addClass("icon-eye");
                }
            });


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


            // On Remove Avatar
            $('#remove-avatar').on("click", function (e) {

                e.stopPropagation();
                let id = $(this).data('id');
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.labels.able_to_revert') }}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-primary ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ url(config('app.admin_path').'/customers')}}" + '/' + id + '/remove-avatar',
                            type: "POST",
                            data: {
                                _method: 'POST',
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                showResponseMessage(data);
                                setTimeout(function () {
                                    location.reload();
                                }, 5000);
                            },
                            error: function (reject) {
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
                    }
                })
            });

        });
    </script>

@endsection

