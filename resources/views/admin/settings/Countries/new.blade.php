@extends('layouts/contentLayoutMaster')

@section('title', __('locale.buttons.add_new'))


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
                        <h4 class="card-title">{{ __('locale.buttons.add_new') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('admin.countries.store') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="name" class="form-label required">{{__('locale.labels.name')}}</label>
                                                <select class="form-control select2" id="name" name="name">
                                                    @foreach($countries as $country)
                                                        <option value="{{$country['name']}}"> {{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('name')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="iso_code" class="form-label required">{{__('locale.labels.iso_code')}}</label>
                                                <select class="form-select select2" id="iso_code" name="iso_code">
                                                    @foreach($countries as $country)
                                                        <option value="{{$country['code']}}"> {{ $country['code'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('iso_code')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="country_code" class="form-label required">{{__('locale.labels.country_code')}}</label>
                                                <select class="form-select select2" id="country_code" name="country_code">
                                                    @foreach($countries as $country)
                                                        <option value="{{ltrim($country['d_code'], '+')}}"> {{ $country['d_code'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('country_code')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="status" class="form-label required">{{ __('locale.labels.status') }}</label>
                                                <select class="form-select" name="status" id="status">
                                                    <option value="1">{{ __('locale.labels.active') }}</option>
                                                    <option value="0">{{ __('locale.labels.disable')}} </option>
                                                </select>
                                                @error('status')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mb-1"><i data-feather="save"></i> {{ __('locale.buttons.save') }}</button>
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

        });
    </script>
@endsection
