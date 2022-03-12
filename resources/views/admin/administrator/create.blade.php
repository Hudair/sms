@extends('layouts/contentLayoutMaster')

@section('title', __('locale.administrator.create_administrator'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.administrator.create_administrator') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.administrators.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="email" class="form-label required">{{__('locale.labels.email')}}</label>
                                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" placeholder="{{__('locale.labels.email')}}" required>
                                            @error('email')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required" for="password">{{ __('locale.labels.password') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password" required/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>

                                            @error('password')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required" for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                       value="{{ old('password_confirmation') }}"
                                                       name="password_confirmation" required/>
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required" for="first_name">{{ __('locale.labels.first_name') }}</label>
                                            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder="{{ __('locale.labels.first_name') }}" value="{{ old('first_name') }}" required autocomplete="first_name"/>
                                            @error('first_name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="last_name">{{ __('locale.labels.last_name') }}</label>
                                            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder="{{ __('locale.labels.last_name') }}" value="{{ old('last_name') }}" autocomplete="last_name"/>

                                            @error('last_name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required" for="phone">{{ __('locale.labels.phone') }}</label>
                                            <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" required placeholder="{{__('locale.labels.phone')}}">

                                            @error('phone')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="role" class="form-label required">{{__('locale.labels.roles')}}</label>
                                            <select class="select2 w-100" id="role" name="roles[]">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}"> {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('roles')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="status" class="form-label required">{{ __('locale.labels.status') }}</label>
                                            <select class="form-select" name="status" id="status">
                                                <option value="1">{{ __('locale.labels.active') }}</option>
                                                <option value="0">{{ __('locale.labels.inactive')}} </option>
                                            </select>
                                            @error('status')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="image" class="form-label">{{__('locale.labels.image')}}</label>
                                            <input type="file" name="image" class="form-control" id="image" accept="image/*"/>
                                            @error('image')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                            <p><small class="text-primary"> {{__('locale.customer.profile_image_size')}} </small></p>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <p class="mb-0">{{ __('locale.administrator.create_customer_account') }}?</p>
                                            <p><small class="text-primary">{{ __('locale.administrator.create_customer_account_associated_admin') }}</small></p>

                                            <div class="form-check form-switch form-switch-md form-check-primary">
                                                <input type="checkbox" class="form-check-input" id="customer" name="is_customer"/>
                                                <label class="form-check-label" for="customer">
                                                    <span class="switch-icon-left">{{ __('locale.labels.yes') }}</span>
                                                    <span class="switch-icon-right">{{ __('locale.labels.no') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 mt-1">
                                        <input type="hidden" value="1" name="is_admin">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i data-feather="save" class="align-middle me-sm-25 me-0"></i> {{__('locale.buttons.save')}}</button>
                                    </div>


                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection


@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection


@section('page-script')

    <script>
        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

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
    </script>
@endsection
