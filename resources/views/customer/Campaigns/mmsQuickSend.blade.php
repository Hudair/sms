@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Quick Send'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')

    <style>
        .customized_select2 .select2-container--classic .select2-selection--single {
            border-left: 0;
            border-radius: 0 4px 4px 0;
        }
    </style>

@endsection

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts campaign_builder">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('customer.mms.quick_send') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="recipient" class="required">{{ __('locale.labels.recipient') }}</label>
                                                <input type="text"
                                                       id="recipient"
                                                       class="form-control @error('recipient') is-invalid @enderror"
                                                       value="{{ old('recipient',  isset($recipient) ? $recipient : null) }}"
                                                       name="recipient"
                                                       required
                                                       placeholder="{{__('locale.labels.required')}}"
                                                       autofocus
                                                >
                                                @error('recipient')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if(auth()->user()->customer->getOption('sender_id_verification') == 'yes')
                                            <div class="col-12">
                                                <p class="text-uppercase">{{ __('locale.labels.originator') }}</p>
                                            </div>

                                            @can('view_sender_id')
                                                <div class="col-6 customized_select2">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <div class="vs-radio-con">
                                                                        <input type="radio" name="originator" checked
                                                                               class="sender_id" value="sender_id">
                                                                        <span class="vs-radio vs-radio-sm">
                                                                            <span class="vs-radio--border"></span>
                                                                            <span class="vs-radio--circle"></span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <select class="form-control select2"
                                                                        id="sender_id"
                                                                        name="sender_id"
                                                                >
                                                                    @foreach($sender_ids as $sender_id)
                                                                        <option value="{{$sender_id->sender_id}}"> {{ $sender_id->sender_id }} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan

                                            @can('view_numbers')
                                                <div class="col-6 customized_select2">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">
                                                                    <div class="vs-radio-con">
                                                                        <input type="radio" name="originator"
                                                                               class="phone_number"
                                                                               value="phone_number">
                                                                        <span class="vs-radio vs-radio-sm">
                                                                            <span class="vs-radio--border"></span>
                                                                            <span class="vs-radio--circle"></span>
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <select class="form-control select2"
                                                                        id="phone_number" name="phone_number"
                                                                        disabled>
                                                                    @foreach($phone_numbers as $number)
                                                                        <option value="{{ $number->number }}"> {{ $number->number }} </option>
                                                                    @endforeach
                                                                </select>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan

                                        @else
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="sender_id">{{__('locale.labels.sender_id')}}</label>
                                                    <input type="text" id="sender_id"
                                                           class="form-control @error('sender_id') is-invalid @enderror"
                                                           name="sender_id">
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
                                                <label for="message" class="required">{{__('locale.labels.message')}}</label>
                                                <textarea class="form-control" name="message" rows="5" id="message"></textarea>
                                                <small class="text-primary text-uppercase" id="remaining">160 {{ __('locale.labels.characters_remaining') }}</small>
                                                <small class="text-primary text-uppercase pull-right" id="messages">1 {{ __('locale.labels.message') }} (s)</small>
                                                @error('message')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="mms_file" class="required">{{ __('locale.labels.mms_file') }}</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="mms_file" name="mms_file" accept="image/*">
                                                    <label class="custom-file-label" for="mms_file">{{ __('locale.labels.choose_file') }}</label>
                                                </div>

                                                @error('mms_file')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" value="mms" name="sms_type">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-send"></i> {{ __('locale.buttons.send') }}
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
                theme: "classic",
                placeholder: "{{ __('locale.labels.choose_your_option') }}"
            });

            $('.select2-search__field').removeAttr("style");

            let $remaining = $('#remaining'),
                $messages = $remaining.next(),
                maxCharInitial = 160,
                maxChar = 157,
                messages = 1,
                $get_msg = $("#message"),
                firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            function get_character() {

                maxCharInitial = 160;
                maxChar = 157;
                messages = 1;

                let totalChar = $get_msg[0].value.length;
                let remainingChar = maxCharInitial;

                if (totalChar <= maxCharInitial) {
                    remainingChar = maxCharInitial - totalChar;
                    messages = 1;
                } else {
                    totalChar = totalChar - maxCharInitial;
                    messages = Math.ceil(totalChar / maxChar);
                    remainingChar = messages * maxChar - totalChar;
                    messages = messages + 1;
                }

                $remaining.text(remainingChar + " {!! __('locale.labels.characters_remaining') !!}");
                $messages.text(messages + " {!! __('locale.labels.message') !!}" + '(s)');
            }

            $get_msg.keyup(get_character);

        });
    </script>
@endsection
