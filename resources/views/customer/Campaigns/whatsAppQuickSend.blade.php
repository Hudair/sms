@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Quick Send'))

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
    <section id="basic-vertical-layouts campaign_builder">
        <div class="row match-height">
            <div class="col-md-8 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('customer.whatsapp.quick_send') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="sending_server" class="form-label required">{{ __('locale.labels.sending_server') }}</label>
                                            <select class="select2 form-select" name="sending_server">
                                                @foreach($sending_server as $server)
                                                    <option value="{{$server->id}}"> {{ $server->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('sending_server')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="recipient" class="form-label required">{{ __('locale.labels.recipient') }}</label>
                                            <div class="input-group">
                                                <div style="width: 8rem">
                                                    <select class="form-select select2" id="country_code" name="country_code">
                                                        @foreach($coverage as $code)
                                                            <option value="{{ $code->country_id }}"> +{{ $code->country->country_code }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <input type="text"
                                                       id="recipient"
                                                       class="form-control @error('recipient') is-invalid @enderror"
                                                       value="{{ old('recipient',  $recipient ?? null) }}"
                                                       name="recipient"
                                                       required
                                                       placeholder="{{__('locale.labels.required')}}"
                                                >

                                            </div>

                                            @error('recipient')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                            @error('country_code')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
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
                                                                <input type="radio" class="form-check-input sender_id" name="originator" checked value="sender_id" id="sender_id_check"/>
                                                                <label class="form-check-label" for="sender_id_check"></label>
                                                            </div>
                                                        </div>


                                                        <div style="width: 17rem">
                                                            <select class="form-select select2" id="sender_id" name="sender_id">
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
                                            <div class="col-md-6 col-12 customized_select2">
                                                <div class="mb-1">
                                                    <label for="phone_number" class="form-label">{{ __('locale.menu.Phone Numbers') }}</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <div class="form-check">
                                                                <input type="radio" class="form-check-input phone_number" value="phone_number" name="originator" id="phone_number_check"/>
                                                                <label class="form-check-label" for="phone_number_check"></label>
                                                            </div>
                                                        </div>

                                                        <div style="width: 17rem">
                                                            <select class="form-select select2" disabled id="phone_number" name="phone_number">
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
                                            <div class="mb-1">
                                                <label for="sender_id" class="form-label">{{__('locale.labels.sender_id')}}</label>
                                                <input type="text" id="sender_id"
                                                       class="form-control @error('sender_id') is-invalid @enderror"
                                                       name="sender_id">
                                                @error('sender_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="message" class="required form-label">{{__('locale.labels.message')}}</label>
                                            <textarea class="form-control" name="message" rows="5" id="message"></textarea>
                                            <small class="text-primary text-uppercase" id="remaining">160 {{ __('locale.labels.characters_remaining') }}</small>
                                            <small class="text-primary text-uppercase pull-right" id="messages">1 {{ __('locale.labels.message') }} (s)</small>
                                            @error('message')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="mms_file" class="form-label">{{__('locale.labels.mms_file')}}</label>
                                            <input type="file" name="mms_file" class="form-control" id="mms_file" accept="image/*,video/*"/>
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
                                        <input type="hidden" value="whatsapp" name="sms_type" id="sms_type">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i data-feather="send"></i> {{ __('locale.buttons.send') }}
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

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/sms-counter.js')) }}"></script>
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
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    // the following code is used to disable x-scrollbar when click in select input and
                    // take 100% width in responsive also
                    dropdownAutoWidth: true,
                    width: '100%',
                    dropdownParent: $this.parent()
                });
            });

            let $remaining = $('#remaining'),
                $get_msg = $("#message"),
                $messages = $remaining.next(),
                firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            $get_msg.on('change keyup paste', function () {
                let data = SmsCounter.count($(this).val(), true);
                $remaining.text(data.remaining + " {!! __('locale.labels.characters_remaining') !!}");
                $messages.text(data.messages + " {!! __('locale.labels.message') !!}" + '(s)');

            });
        });
    </script>
@endsection
