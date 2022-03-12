@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Send Using File'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">

    <style>
        .customized_select2 .select2-selection--multiple {
            border-left: 0;
            border-radius: 0 4px 4px 0;
            min-height: 2.75rem !important;
        }

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

                            <form class="form form-vertical" action="{{ route('customer.whatsapp.import') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <p class="text-uppercase">{{ __('locale.labels.sample_file') }}</p>
                                            <a href="{{route('sample.file')}}" class="btn btn-primary px-1 py-1 waves-effect waves-light btn-md fw-bold">
                                                <i data-feather="file-text"></i> {{ __('locale.labels.download_sample_file') }}
                                            </a>

                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="name" class="required form-label">{{ __('locale.labels.name') }}</label>
                                            <input type="text"
                                                   id="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name') }}"
                                                   name="name" required
                                                   placeholder="{{__('locale.labels.required')}}" autofocus>
                                            @error('name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

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
                                                            <select class="form-select select2" id="sender_id" name="sender_id[]">
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
                                                            <select class="form-select select2" disabled id="phone_number" name="phone_number[]" multiple>
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
                                                       name="sender_id[]">
                                                @error('sender_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif



                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="import_file" class="form-label">{{ __('locale.labels.import_file') }}</label>
                                            <div class="us-file-zone us-clickable">
                                                <input type="file" name="import_file" class="us-file upload-file" id="import_file" accept="text/csv">
                                                <div class="us-file-message">{{__('locale.filezone.click_here_to_upload')}}</div>
                                                <div class="us-file-footer">
                                                    {!! __('locale.campaigns.import_file_description') !!}
                                                    {!! __('locale.contacts.only_supported_file') !!}
                                                </div>
                                            </div>
                                            @error('import_file')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                            @enderror

                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" checked value="true" class="form-check-input" name="header">
                                                <label class="form-check-label">{{ __('locale.filezone.file_contains_header_row') }}?</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input schedule" value="true" name="schedule" {{ old('schedule') == true ? "checked" : null }}>
                                                <label class="form-check-label">{{__('locale.campaigns.schedule_campaign')}}?</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row schedule_time">
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <label for="schedule_date" class="form-label">{{ __('locale.labels.date') }}</label>
                                            <input type="text" id="schedule_date" name="schedule_date" class="form-control schedule_date" placeholder="YYYY-MM-DD"/>
                                            @error('schedule_date')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <label for="time" class="form-label">{{ __('locale.labels.time') }}</label>
                                            <input type="text" id="time" class="form-control flatpickr-time text-start" name="schedule_time" placeholder="HH:MM"/>
                                            @error('schedule_time')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="timezone" class="form-label">{{__('locale.labels.timezone')}}</label>
                                            <select class="form-select select2" id="timezone" name="timezone">
                                                @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                    <option value="{{$timezone['zone']}}" {{ Auth::user()->timezone == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('timezone')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" value="whatsapp" name="sms_type" id="sms_type">
                                        <input type="hidden" value="{{$plan_id}}" name="plan_id">
                                        <button type="submit" class="btn btn-primary mt-1 mb-1"><i data-feather="send"></i> {{ __('locale.buttons.send') }}
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
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/dom-rules.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {

            $("body").on("change", ".upload-file", function (e) {
                if ($(this).val() !== '') {
                    $('.us-file-message').addClass('us-file-message-done');
                } else {
                    $('.us-file-message').removeClass('us-file-message-done');
                }

            });

            $('.schedule_date').flatpickr({
                minDate: "today",
                dateFormat: "Y-m-d",
                defaultDate: "{{ date('Y-m-d') }}",
            });

            $('.flatpickr-time').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                defaultDate: "{{ date('H:i') }}",
            });

            $(".sender_id").on("click", function () {
                $("#sender_id").prop("disabled", !this.checked);
                $("#phone_number").prop("disabled", this.checked);
            });

            $(".phone_number").on("click", function () {
                $("#phone_number").prop("disabled", !this.checked);
                $("#sender_id").prop("disabled", this.checked);
            });


            let schedule = $('.schedule'),
                scheduleTime = $(".schedule_time");

            if (schedule.prop('checked') === true) {
                scheduleTime.show();
            } else {
                scheduleTime.hide();
            }

            schedule.change(function () {
                scheduleTime.fadeToggle();
            });


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
