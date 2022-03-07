@extends('layouts/contentLayoutMaster')
@section('title', __('locale.blacklist.add_new_blacklist'))

@section('page-style')
    <style>
        input[type=radio] {
            box-sizing: border-box;
            padding: 0;
            position: absolute;
            pointer-events: none;
            clip: rect(0, 0, 0, 0);
        }

        label.active.btn {
            color: #ffffff !important;
            background-color: #7E57C2 !important;
            border-color: #7E57C2 !important;
        }
    </style>
@endsection

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.blacklist.add_new_blacklist') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.blacklist') !!} {{config('app.name')}}</p>

                            <form class="form form-vertical" action="{{ route('admin.blacklists.store') }}"
                                  method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="number" class="required">{{ __('locale.labels.paste_numbers') }}</label>
                                                <textarea id="number" class="form-control @error('number') is-invalid @enderror" name="number" required autofocus></textarea>
                                                @error('number')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="btn-group btn-group-sm" data-toggle="buttons">

                                                    <label class="btn btn-outline-primary active">
                                                        <input type="radio" name="delimiter" value="," checked>, ({{ __('locale.labels.comma') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value=";">; ({{ __('locale.labels.semicolon') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value="|">| ({{ __('locale.labels.bar') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value="tab">{{__('locale.labels.tab')}}
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value="new_line">{{ __('locale.labels.new_line') }}
                                                    </label>

                                                    @error('delimiter')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror

                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reason">{{ __('locale.labels.reason') }}</label>
                                                <input type="text" id="reason"
                                                       class="form-control @error('reason') is-invalid @enderror"
                                                       value="{{ old('reason')}}"
                                                       name="reason">
                                                @error('reason')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}
                                            </button>

                                            <button type="reset" class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-refresh-cw"></i> {{ __('locale.buttons.reset') }}
                                            </button>
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

@section('page-script')

    <script>
        $(document).ready(function () {

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>
@endsection
