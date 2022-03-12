@extends('layouts/contentLayoutMaster')

@section('title', __('locale.keywords.update_keyword'))


@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <style>
        .customized_select2 .select2-selection--single {
            border-left: 0;
            border-radius: 0 4px 4px 0;
            min-height: 2.75rem !important;
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
                                            <div class="mb-1">
                                                <label for="title" class="form-label">{{ __('locale.labels.title') }}</label>
                                                <input type="text" id="title" class="form-control" value="{{ $keyword->title }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="keyword_name"  class="form-label">{{ __('locale.labels.keyword') }}</label>
                                                <input type="text" id="keyword_name" class="form-control" value="{{ $keyword->keyword_name}}" readonly>
                                            </div>
                                        </div>



                                        @if(auth()->user()->customer->getOption('sender_id_verification') == 'yes')
                                            <div class="col-12">
                                                <p class="text-uppercase">{{ __('locale.labels.originator') }}</p>
                                            </div>

                                            @can('view_sender_id')
                                                <div class="col-md-6 col-12 customized_select2">
                                                    <div class="mb-1">
                                                        <label for="sender_id" class="form-label">{{ __('locale.labels.sender_id') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input sender_id" name="originator" @if( !is_numeric($keyword->sender_id) && !is_null($keyword->sender_id)) checked @endif value="sender_id" id="sender_id_check"/>
                                                                    <label class="form-check-label" for="sender_id_check"></label>
                                                                </div>
                                                            </div>

                                                            <select class="form-select select2" id="sender_id" name="sender_id" @if( is_numeric($keyword->sender_id) || is_null($keyword->sender_id)) disabled @endif>
                                                                @foreach($sender_ids as $sender_id)
                                                                    <option value="{{$sender_id->sender_id}}" {{ $sender_id->sender_id == $keyword->sender_id ? 'selected': null  }}>
                                                                        {{ $sender_id->sender_id }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan

                                            @can('view_numbers')
                                                <div class="col-md-6 col-12 customized_select2">
                                                    <div class="mb-1">
                                                        <label for="phone_number" class="form-label">{{ __('locale.menu.Phone Numbers') }}</label>
                                                        <div class="input-group">
                                                            <div class="input-group-text">
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input phone_number" @if( is_numeric($keyword->sender_id)) checked @endif value="phone_number" name="originator" id="phone_number_check"/>
                                                                    <label class="form-check-label" for="phone_number_check"></label>
                                                                </div>
                                                            </div>

                                                            <select class="form-select select2" @if( !is_numeric($keyword->sender_id)) disabled @endif id="phone_number" name="phone_number">
                                                                @foreach($phone_numbers as $number)
                                                                    <option value="{{ $number->number }}" {{ $number->number == $keyword->sender_id ? 'selected': null  }}> {{ $number->number }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan

                                        @else
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label for="sender_id" class="form-label">{{__('locale.labels.sender_id')}}</label>
                                                    <input type="text" id="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ $keyword->sender_id }}" name="sender_id">
                                                    @error('sender_id')
                                                    <p><small class="text-danger">{{ $message }}</small></p>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="reply_text" class="form-label">{{__('locale.keywords.reply_text_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_text"> {{old('reply_text', $keyword->reply_text ?? null)}} </textarea>

                                                @error('reply_text')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="reply_voice" class="form-label">{{__('locale.keywords.reply_voice_recipient')}}</label>
                                                <textarea class="form-control" rows="3" name="reply_voice"> {{old('reply_voice', $keyword->reply_voice ?? null)}} </textarea>

                                                @error('reply_voice')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if(isset($keyword->reply_mms))
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label  class="form-label">{{ __('locale.labels.mms_file') }}</label>
                                                    <p><a href="{{$keyword->reply_mms}}" target="_blank">{{$keyword->reply_mms}}</a></p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label for="reply_mms" class="form-label required">{{__('locale.labels.mms_file')}}</label>
                                                <input type="file" name="reply_mms" class="form-control" id="reply_mms" accept="image/*,video/*"/>
                                                @error('reply_mms')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
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

            $(".sender_id").on("click", function () {
                $("#sender_id").prop("disabled", !this.checked);
                $("#phone_number").prop("disabled", this.checked);
            });

            $(".phone_number").on("click", function () {
                $("#phone_number").prop("disabled", !this.checked);
                $("#sender_id").prop("disabled", this.checked);
            });


            // Basic Select2 select
            $(".select2").each(function () {
                let $this = $(this);
                $this.wrap('<div class="position-relative" style="width: 80%"></div>');
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
