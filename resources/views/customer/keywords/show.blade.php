@extends('layouts/contentLayoutMaster')

@section('title', __('locale.keywords.update_keyword'))


@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <style>
        .select2-container--classic .select2-selection--single {
            border-left: 0;
            border-radius: 0 4px 4px 0;
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
                        <h4 class="card-title">{{ __('locale.keywords.update_keyword') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{!!  __('locale.description.keywords') !!} {{config('app.name')}}</p>

                            <form class="form form-vertical" action="{{ route('customer.keywords.update',  $keyword->uid) }}" method="post" enctype="multipart/form-data">
                                {{ method_field('PUT') }}
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="title">{{ __('locale.labels.title') }}</label>
                                                <input type="text" id="title" class="form-control" value="{{ $keyword->title }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keyword_name">{{ __('locale.labels.keyword') }}</label>
                                                <input type="text" id="keyword_name" class="form-control" value="{{ $keyword->keyword_name}}" readonly>
                                            </div>
                                        </div>


                                        @if(auth()->user()->customer->getOption('sender_id_verification') == 'yes')
                                            <div class="col-12">
                                                <p class="text-uppercase">{{ __('locale.labels.originator') }}</p>
                                            </div>

                                            @can('view_sender_id')
                                                <div class="col-sm-6 col-12 mb-1">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <div class="vs-radio-con">
                                                                        <input type="radio"
                                                                               name="originator"
                                                                               @if( !is_numeric($keyword->sender_id) && !is_null($keyword->sender_id)) checked @endif
                                                                               class="sender_id"
                                                                               value="sender_id"
                                                                        >
                                                                        <span class="vs-radio vs-radio-sm">
                                                                            <span class="vs-radio--border"></span>
                                                                            <span class="vs-radio--circle"></span>
                                                                        </span>
                                                                    </div>
                                                                </div>


                                                                <select class="form-control select2" id="sender_id" name="sender_id" @if( is_numeric($keyword->sender_id) || is_null($keyword->sender_id)) disabled @endif>
                                                                    <option>{{ __('locale.labels.sender_id') }}</option>
                                                                    @foreach($sender_ids as $sender_id)
                                                                        <option value="{{$sender_id->sender_id}}" {{ $sender_id->sender_id == $keyword->sender_id ? 'selected': null  }}> {{ $sender_id->sender_id }} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            @endcan

                                            @can('view_numbers')
                                                <div class="col-sm-6 col-12 mb-1">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <div class="vs-radio-con">
                                                                        <input
                                                                                type="radio"
                                                                                name="originator"
                                                                                class="phone_number"
                                                                                @if( is_numeric($keyword->sender_id)) checked @endif
                                                                                value="phone_number"
                                                                        >
                                                                        <span class="vs-radio vs-radio-sm">
                                                                            <span class="vs-radio--border"></span>
                                                                            <span class="vs-radio--circle"></span>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <select class="form-control select2" id="phone_number" name="phone_number" @if( !is_numeric($keyword->sender_id)) disabled @endif >
                                                                    <option> {{ __('locale.labels.shared_number') }}</option>
                                                                    @foreach($phone_numbers as $number)
                                                                        <option value="{{ $number->number }}" {{ $number->number == $keyword->sender_id ? 'selected': null  }}> {{ $number->number }} </option>
                                                                    @endforeach
                                                                </select>

                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>
                                            @endcan

                                        @else
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="sender_id">{{__('locale.labels.sender_id')}}</label>
                                                    <input type="text" id="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ $keyword->sender_id }}" name="sender_id">
                                                    @error('sender_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reply_text">{{__('locale.keywords.reply_text_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_text"> {{old('reply_text', isset($keyword->reply_text) ? $keyword->reply_text : null)}} </textarea>

                                                @error('reply_text')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reply_voice">{{__('locale.keywords.reply_voice_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_voice"> {{old('reply_voice', isset($keyword->reply_voice) ? $keyword->reply_voice : null)}} </textarea>

                                                @error('reply_voice')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if(isset($keyword->reply_mms))
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>{{ __('locale.labels.mms_file') }}</label>
                                                    <p><a href="{{$keyword->reply_mms}}" target="_blank">{{$keyword->reply_mms}}</a></p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="reply_mms">{{ __('locale.keywords.reply_mms_recipient') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" name="reply_mms" class="custom-file-input" id="reply_mms" accept="image/*">
                                                    <label class="custom-file-label" for="reply_mms" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>

                                                    @error('reply_mms')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </fieldset>
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

            $(".sender_id").on("click", function () {
                $("#sender_id").prop("disabled", !this.checked);
                $("#phone_number").prop("disabled", this.checked);
            });

            $(".phone_number").on("click", function () {
                $("#phone_number").prop("disabled", !this.checked);
                $("#sender_id").prop("disabled", this.checked);
            });


            $(".select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                theme: "classic"
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
