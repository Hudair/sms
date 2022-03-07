@extends('layouts/contentLayoutMaster')

@section('title', __('locale.settings.add_new'))


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
                        <h4 class="card-title">{{ __('locale.settings.add_new') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('admin.languages.store') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="language" class="required">{{__('locale.labels.language')}}</label>
                                                <select class="form-control select2" id="language" name="language">
                                                    @foreach(\App\Models\Language::languageCodes() as $language)
                                                        <option value="{{$language['code']}}"> {{ $language['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('language')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="country" class="required">{{__('locale.labels.country')}}</label>
                                                <select class="form-control select2" id="country" name="country">
                                                    @foreach(\App\Helpers\Helper::countries() as $country)
                                                        <option value="{{$country['code']}}"> {{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('country')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status" class="required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="1">{{ __('locale.labels.active') }}</option>
                                                    <option value="0">{{ __('locale.labels.disable')}} </option>
                                                </select>
                                                @error('status')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                        </div>

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
        $(document).ready(function () {

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

        });
    </script>
@endsection
