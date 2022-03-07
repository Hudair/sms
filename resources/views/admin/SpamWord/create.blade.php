@extends('layouts/contentLayoutMaster')
@section('title', __('locale.spam_word.add_new_word'))

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.spam_word.add_new_word') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('admin.spam-word.store') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="word" class="required">{{ __('locale.menu.Spam Word') }}</label>
                                                <input id="word" class="form-control @error('word') is-invalid @enderror" name="word" required autofocus>
                                                @error('word')
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
