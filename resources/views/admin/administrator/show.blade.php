@extends('layouts/contentLayoutMaster')

@section('title', __('locale.administrator.update_administrator'))

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
                        <h4 class="card-title">{{ __('locale.administrator.update_administrator') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.administrators.update', $administrator->uid)  }}" method="post" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="email" class="form-label required">{{__('locale.labels.email')}}</label>
                                            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $administrator->email }}" name="email" required>
                                            @error('email')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="password">{{ __('locale.labels.password') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">
                                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password">
                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>

                                            @if($errors->has('password'))
                                                <p><small class="text-danger">{{ $errors->first('password') }}</small></p>
                                            @else
                                                <p><small class="text-primary"> {{__('locale.customer.leave_blank_password')}} </small></p>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                            <div class="input-group input-group-merge form-password-toggle">

                                                <input type="password" id="password_confirmation"
                                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                                       value="{{ old('password_confirmation') }}"
                                                       name="password_confirmation"
                                                >

                                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required" for="first_name">{{ __('locale.labels.first_name') }}</label>
                                            <input type="text" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ $administrator->first_name }}" name="first_name" required/>
                                            @error('first_name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label" for="last_name">{{ __('locale.labels.last_name') }}</label>
                                            <input type="text" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ $administrator->last_name }}" name="last_name"/>

                                            @error('last_name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="role" class="form-label required">{{__('locale.labels.roles')}}</label>
                                            <select class="select2 w-100" id="role" name="roles[]">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" @if($get_roles == $role->id) selected @endif> {{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('roles')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="timezone" class="form-label required">{{__('locale.labels.timezone')}}</label>
                                            <select class="select2 w-100" id="timezone" name="timezone">
                                                @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                    <option value="{{$timezone['zone']}}" {{ $administrator->timezone == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('timezone')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="locale" class="form-label required">{{__('locale.labels.language')}}</label>
                                            <select class="select2 w-100" id="locale" name="locale">
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->code }}" {{ $administrator->locale == $language->code ? 'selected': null }}> {{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('locale')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
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

                                    <div class="col-12 mt-1">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i data-feather="save" class="align-middle me-sm-25 me-0"></i> {{__('locale.buttons.update')}}</button>
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
        let showHideInput = $('.show_hide_password input');
        let showHideIcon = $('.show_hide_password i');

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
