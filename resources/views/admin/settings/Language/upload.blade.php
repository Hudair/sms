@extends('layouts/contentLayoutMaster')

@section('title', __('locale.settings.upload_language'))

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.settings.upload_language') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>Language upload file should be a <code>zip package</code> containing translation files: 1. <code>locale.php</code> and 2. <code>validation.php</code></p>

                            <form class="form form-vertical" action="{{ route('admin.languages.upload', $language->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="file">{{ __('locale.settings.upload_file') }}</label>
                                            <div class="custom-file">
                                                <input type="file" name="file" class="custom-file-input" id="file" accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed">
                                                <label class="custom-file-label" for="file" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>
                                                @error('file')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
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
    </section>
    <!-- // Basic Vertical form layout section end -->

@endsection
