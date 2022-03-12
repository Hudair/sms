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
                                        <div class="mb-1">
                                            <input type="file" required name="file" class="form-control" id="file" accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed">
                                            @error('file')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary mb-1">
                                            <i data-feather="upload"></i> {{__('locale.labels.upload')}}
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
