@extends('layouts/contentLayoutMaster')

@section('title', __('locale.customer.add_new'))

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
                        <h4 class="card-title"> {{__('locale.customer.add_new')}} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="form-body">
                                <form class="form form-vertical" action="{{ route('admin.customers.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="email" class="required">{{__('locale.labels.email')}}</label>
                                                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" required>
                                                @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12 form-group">
                                            <label for="password" class="required">{{__('locale.labels.password')}}</label>
                                            <div class="position-relative show_hide_password">
                                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password" required>
                                                <div class="form-control-position cursor-pointer">
                                                    <i class="feather icon-eye-off"></i>
                                                </div>
                                                @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="password_confirmation" class="required">{{__('locale.labels.password_confirmation')}}</label>
                                                <div class="position-relative show_hide_password">
                                                    <input type="password" id="password_confirmation"
                                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                                           value="{{ old('password_confirmation') }}"
                                                           name="password_confirmation" required>
                                                    <div class="form-control-position cursor-pointer">
                                                        <i class="feather icon-eye-off"></i>
                                                    </div>
                                                    @error('password_confirmation')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="first_name" class="required">{{__('locale.labels.first_name')}}</label>
                                                <input type="text" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" name="first_name" required>
                                                @error('first_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="last_name">{{__('locale.labels.last_name')}}</label>
                                                <input type="text" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" name="last_name">
                                                @error('last_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="phone" class="required">{{__('locale.labels.phone')}}</label>
                                                <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" name="phone" required>
                                                @error('phone')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="timezone" class="required">{{__('locale.labels.timezone')}}</label>
                                                <select class="form-control select2" id="timezone" name="timezone">
                                                    @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                        <option value="{{$timezone['zone']}}" {{ config('app.timezone') == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                            @error('timezone')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="locale" class="required">{{__('locale.labels.language')}}</label>
                                                <select class="form-control select2" id="locale" name="locale">
                                                    @foreach($languages as $language)
                                                        <option value="{{ $language->code }}" {{old('locale') == $language->code ? 'selected': null }}> {{ $language->name }}</option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                            @error('locale')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status" class="required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1">{{ __('locale.labels.active') }}</option>
                                                    <option value="0">{{ __('locale.labels.inactive')}} </option>
                                                </select>
                                                @error('status')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="image">{{ __('locale.labels.image') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="image" class="custom-file-input" id="image" accept="image/*">
                                                    <label class="custom-file-label" for="image" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>
                                                    @error('image')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <p><small class="text-primary"> {{__('locale.customer.profile_image_size')}} </small></p>
                                            </div>
                                        </div>


                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1">
                                                <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                                            </button>
                                        </div>


                                    </div>
                                </form>
                            </div>

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
    </script>
@endsection
