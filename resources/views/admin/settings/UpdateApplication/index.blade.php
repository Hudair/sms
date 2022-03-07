@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Update Application'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">

                            @if(config('app.version') == '3.0.1')
                                <h4> Your are currently running on Ultimate SMS <code class="font-weight-bolder">{{ config('app.version') }}</code> You are already in a latest version</h4>
                            @else


                                <p> Your are currently running on Ultimate SMS <code class="font-weight-bolder">{{ config('app.version') }}</code> To upgrade your application, please download the latest build from <a href="https://codecanyon.net/item/ultimate-sms-bulk-sms-application-for-marketing/20062631" target="_blank">codecanyon.net</a> , then you can find the upgrade package located at
                                    downloaded folder called <code>3.*.*_update.zip</code>, upload it using the form below.
                                </p>

                                <p>
                                    Please make sure the upgrade package file size does not exceed the following upload limit settings:<br><br>
                                    <span class="font-weight-bolder text-danger"><i class="feather icon-check-square"></i> post_max_size</span> <span class="font-weight-bolder text-primary">256M</span><br>
                                    <span class="font-weight-bolder text-danger"><i class="feather icon-check-square"></i> upload_max_filesize</span> <span class="font-weight-bolder text-primary">256M</span>
                                </p>


                                <form class="form form-vertical" action="#" method="post">
                                    @csrf
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="phone" class="required">{{__('locale.labels.phone')}}</label>
                                                <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" name="phone" required>
                                                @error('phone')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label for="first_name">{{__('locale.labels.first_name')}}</label>
                                                <input type="text" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" name="first_name">
                                                @error('first_name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
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
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1">
                                                <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection


@section('page-script')

    <script>
        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

    </script>
@endsection
