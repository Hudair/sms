@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Send Using File'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')

    <style>
        .customized_select2 .select2-container--classic .select2-selection--multiple, .select2-container--default .select2-selection--multiple {
            border-left: 0;
            border-radius: 0 4px 4px 0;
        }

        .sender_id_select2 .customized_select2 .select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--single {
            border-left: 0;
            border-radius: 0 4px 4px 0;
        }

    </style>

@endsection

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts bulk_messages">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <form class="form form-vertical" action="{{ route('customer.voice.import') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">

                                            <div class="form-group">
                                                <p class="text-uppercase">{{ __('locale.labels.sample_file') }}</p>
                                                <a href="{{route('sample.file')}}" class="btn btn-primary px-1 py-1 waves-effect waves-light btn-md text-bold-500">
                                                    <i class="feather icon-file-text"></i> {{ __('locale.labels.download_sample_file') }}
                                                </a>

                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text"
                                                       id="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name') }}"
                                                       name="name"
                                                       required
                                                       placeholder="{{__('locale.labels.required')}}"
                                                       autofocus
                                                >
                                                @error('name')
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
                                                                <select class="form-control sender_id_select2"
                                                                        id="sender_id" name="sender_id[]"
                                                                        style="width: auto">
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

                                                                <select class="form-control number_select2"
                                                                        id="phone_number" name="phone_number[]"
                                                                        multiple="multiple" style="width: auto"
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
                                                           name="sender_id[]">
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
                                                <label for="import_file">{{ __('locale.labels.import_file') }}</label>
                                                <div class="us-file-zone us-clickable">
                                                    <input type="file" name="import_file" class="us-file upload-file"
                                                           id="import_file" accept="text/csv">
                                                    <div class="us-file-message">{{__('locale.filezone.click_here_to_upload')}}
                                                    </div>
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


                                        <div class="col-12 mb-1">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" checked value="true" name="header">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">{{ __('locale.filezone.file_contains_header_row') }}?</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">

                                            <div class="form-group">
                                                <label class="required" for="language">{{ __('locale.labels.language') }}</label>
                                                <select class="form-control select2" id="language" name="language" required>
                                                    @foreach(\App\Helpers\Helper::voice_regions() as $key => $value)
                                                        <option value="{{$key}}" {{ $key == 'en-GB' ? 'selected': null }}> {{ $value }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12">

                                            <div class="form-group">
                                                <label class="locale.labels.gender">{{ __('locale.labels.gender') }}</label>
                                                <select class="form-control" id="gender" name="gender">
                                                    <option value="male"> {{ __('locale.labels.male') }}</option>
                                                    <option value="female"> {{ __('locale.labels.female') }}</option>
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" class="schedule" value="true" name="schedule" {{ old('schedule') == true ? "checked" : null }}>
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.campaigns.schedule_campaign')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row schedule_time">

                                        <div class="col-12">

                                            <div class="form-group">
                                                <label for="timezone">{{__('locale.labels.timezone')}}</label>
                                                <select class="form-control select2" id="timezone" name="timezone">
                                                    @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                                                        <option value="{{$timezone['zone']}}" {{ Auth::user()->timezone == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('timezone')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="schedule_date">{{ __('locale.labels.date') }}</label>
                                                <input type="text" id="schedule_date" name="schedule_date" class="form-control schedule_date" placeholder="YYYY-MM-DD"/>
                                                @error('schedule_date')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="time">{{ __('locale.labels.time') }}</label>
                                                <input type="text" id="time" class="form-control flatpickr-time text-left" name="schedule_time" placeholder="HH:MM"/>
                                                @error('schedule_time')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" value="voice" name="sms_type" id="sms_type">
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
                minTime: "{{ date('H:i') }}",
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

            $(".select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                theme: "classic",
                placeholder: "{{ __('locale.labels.choose_your_option') }}"
            });

            $(".sender_id_select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: "{{ __('locale.labels.sender_id') }}"
            });

            $(".number_select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: "{{ __('locale.menu.Phone Numbers') }}"
            });

            $('.select2-search__field').removeAttr("style");

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }
        });
    </script>
@endsection
