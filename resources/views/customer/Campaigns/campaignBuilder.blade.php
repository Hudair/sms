@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Campaign Builder'))

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

                            <form class="form form-vertical" action="{{ route('customer.sms.campaign_builder') }}" method="post">
                                @csrf
                                <div class="row">

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
                                            <label for="contact_groups" class="form-label">{{ __('locale.contacts.contact_groups') }}</label>
                                            <select class="select2 form-select" name="contact_groups[]" multiple="multiple">
                                                @foreach($contact_groups as $group)
                                                    <option value="{{$group->id}}"> {{ $group->name }}
                                                        ({{\App\Library\Tool::number_with_delimiter($group->subscribersCount($group->cache))}} {{__('locale.menu.Contacts')}})
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('contact_groups')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="country_code form-label">{{__('locale.labels.country_code')}}</label>
                                            <select class="form-select select2" id="country_code" name="country_code">
                                                <option value="0">{{ __('locale.labels.remaining_in_number') }}</option>
                                                @foreach($coverage as $code)
                                                    <option value="{{ $code->country_id }}"> +{{ $code->country->country_code }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('country_code')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-12">

                                        <div class="mb-1">
                                            <label for="recipients" class="form-label">{{ __('locale.labels.manual_input') }}</label>
                                            <textarea class="form-control" id="recipients" name="recipients"></textarea>
                                            <p><small class="text-uppercase">
                                                    {{ __('locale.labels.total_number_of_recipients') }}:<span class="number_of_recipients fw-bold text-success">0</span>
                                                </small></p>
                                            @error('recipients')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="btn-group btn-group-sm recipients" role="group">
                                                <input type="radio" class="btn-check" name="delimiter" value="," id="comma" autocomplete="off" checked/>
                                                <label class="btn btn-outline-primary" for="comma">, ({{ __('locale.labels.comma') }})</label>

                                                <input type="radio" class="btn-check" name="delimiter" value=";" id="semicolon" autocomplete="off"/>
                                                <label class="btn btn-outline-primary" for="semicolon">; ({{ __('locale.labels.semicolon') }})</label>

                                                <input type="radio" class="btn-check" name="delimiter" value="|" id="bar" autocomplete="off"/>
                                                <label class="btn btn-outline-primary" for="bar">| ({{ __('locale.labels.bar') }})</label>

                                                <input type="radio" class="btn-check" name="delimiter" value="tab" id="tab" autocomplete="off"/>
                                                <label class="btn btn-outline-primary" for="tab">{{ __('locale.labels.tab') }}</label>

                                                <input type="radio" class="btn-check" name="delimiter" value="new_line" id="new_line" autocomplete="off"/>
                                                <label class="btn btn-outline-primary" for="new_line">{{ __('locale.labels.new_line') }}</label>

                                            </div>

                                            @error('delimiter')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                        <p>
                                            <small class="text-primary">{!! __('locale.description.manual_input') !!} {!! __('locale.contacts.include_country_code_for_successful_import') !!}</small>
                                        </p>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="mb-1">
                                            <label class="sms_template form-label">{{__('locale.permission.sms_template')}}</label>
                                            <select class="form-select select2" id="sms_template">
                                                <option>{{ __('locale.labels.select_one') }}</option>
                                                @foreach($templates as $template)
                                                    <option value="{{$template->id}}">{{ $template->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="mb-1">
                                            <label class="form-label">{{__('locale.labels.available_tag')}}</label>
                                            <select class="form-select select2" id="available_tag">
                                                <option value="phone">{{ __('locale.labels.phone') }}</option>
                                                <option value="first_name">{{ __('locale.labels.first_name') }}</option>
                                                <option value="last_name">{{ __('locale.labels.last_name') }}</option>
                                                <option value="email">{{ __('locale.labels.email') }}</option>
                                                <option value="username">{{ __('locale.labels.username') }}</option>
                                                <option value="company">{{ __('locale.labels.company') }}</option>
                                                <option value="address">{{ __('locale.labels.address') }}</option>
                                                <option value="birth_date">{{ __('locale.labels.birth_date') }}</option>
                                                <option value="anniversary_date">{{ __('locale.labels.anniversary_date') }}</option>

                                                @if($template_tags)
                                                    @foreach($template_tags as $field)
                                                        <option value="{{$field->tag}}">{{ $field->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="message" class="required form-label">{{__('locale.labels.message')}}</label>
                                            <textarea class="form-control" name="message" rows="5" id="message"></textarea>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-primary text-uppercase text-start" id="remaining">160 {{ __('locale.labels.characters_remaining') }}</small>
                                                <small class="text-primary text-uppercase text-end" id="messages">1 {{ __('locale.labels.message') }} (s)</small>
                                            </div>
                                            @error('message')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input schedule" value="true" name="schedule" {{ old('schedule') == true ? "checked" : null }}>
                                                <label class="form-check-label">{{__('locale.campaigns.schedule_campaign')}}?</label>
                                            </div>
                                            <p><small class="text-primary px-2">{{__('locale.campaigns.schedule_campaign_note')}}</small></p>
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

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="frequency_cycle" class="form-label">{{__('locale.labels.frequency')}}</label>
                                            <select class="form-select" id="frequency_cycle" name="frequency_cycle">
                                                <option value="onetime" {{old('frequency_cycle')}}> {{__('locale.labels.one_time')}}</option>
                                                <option value="daily" {{old('frequency_cycle')}}> {{__('locale.labels.daily')}}</option>
                                                <option value="monthly" {{old('frequency_cycle')}}> {{__('locale.labels.monthly')}}</option>
                                                <option value="yearly" {{old('frequency_cycle')}}> {{__('locale.labels.yearly')}}</option>
                                                <option value="custom" {{old('frequency_cycle')}}> {{__('locale.labels.custom')}}</option>
                                            </select>
                                        </div>
                                        @error('frequency_cycle')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6 col-12 show-custom">
                                        <div class="mb-1">
                                            <label for="frequency_amount" class="form-label">{{__('locale.plans.frequency_amount')}}</label>
                                            <input type="text"
                                                   id="frequency_amount"
                                                   class="form-control text-right @error('frequency_amount') is-invalid @enderror"
                                                   name="frequency_amount"
                                                   value="{{ old('frequency_amount') }}"
                                            >
                                            @error('frequency_amount')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-12 show-custom">
                                        <div class="mb-1">
                                            <label for="frequency_unit" class="form-label">{{__('locale.plans.frequency_unit')}}</label>
                                            <select class="form-select" id="frequency_unit" name="frequency_unit">
                                                <option value="day" {{old('frequency_unit')}}> {{__('locale.labels.day')}}</option>
                                                <option value="week" {{old('frequency_unit')}}> {{__('locale.labels.week')}}</option>
                                                <option value="month" {{old('frequency_unit')}}> {{__('locale.labels.month')}}</option>
                                                <option value="year" {{old('frequency_unit')}}> {{__('locale.labels.year')}}</option>
                                            </select>
                                        </div>
                                        @error('frequency_unit')
                                        <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 show-recurring">
                                        <div class="mb-1">
                                            <label for="recurring_date" class="form-label"> {{ __('locale.labels.end_date') }}</label>
                                            <input type="text" id="recurring_date" name="recurring_date" class="form-control schedule_date" placeholder="YYYY-MM-DD"/>
                                            @error('recurring_date')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 show-recurring">
                                        <div class="mb-1">
                                            <label for="recurring_time" class="form-label">{{ __('locale.labels.end_time') }}</label>
                                            <input type="text" id="recurring_time" class="form-control flatpickr-time text-start" name="recurring_time" placeholder="HH:MM"/>
                                            @error('recurring_time')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" name="advanced" class="form-check-input advanced" value="true">
                                                <label class="form-check-label">{{ __('locale.labels.advanced') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row advanced_div">
                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" value="true" name="send_copy" class="form-check-input">
                                                <label class="form-check-label">{{__('locale.campaigns.send_copy_via_email')}}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class="form-check form-check-inline">
                                                <input type="checkbox" value="true" name="create_template" class="form-check-input">
                                                <label class="form-check-label">{{__('locale.campaigns.create_template_based_message')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" value="plain" name="sms_type" id="sms_type">
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

    <script src="{{ asset(mix('js/scripts/sms-counter.js')) }}"></script>

    <script>
        $(document).ready(function () {

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

            $('.advanced_div').hide();

            schedule.change(function () {
                scheduleTime.fadeToggle();
            });

            $('.advanced').change(function () {
                $('.advanced_div').fadeToggle();
            });

            $.createDomRules({

                parentSelector: 'body',
                scopeSelector: 'form',
                showTargets: function (rule, $controller, condition, $targets, $scope) {
                    $targets.fadeIn();
                },
                hideTargets: function (rule, $controller, condition, $targets, $scope) {
                    $targets.fadeOut();
                },

                rules: [
                    {
                        controller: '#frequency_cycle',
                        value: 'custom',
                        condition: '==',
                        targets: '.show-custom',
                    },
                    {
                        controller: '#frequency_cycle',
                        value: 'onetime',
                        condition: '!=',
                        targets: '.show-recurring',
                    },
                    {
                        controller: '.message_type',
                        value: 'mms',
                        condition: '==',
                        targets: '.send-mms',
                    }
                ]
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

            let number_of_recipients_ajax = 0,
                number_of_recipients_manual = 0,
                $get_recipients = $('#recipients'),
                $remaining = $('#remaining'),
                $messages = $remaining.next(),
                $get_msg = $("#message"),
                merge_state = $('#available_tag'),
                firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            function get_delimiter() {
                return $('input[name=delimiter]:checked').val();
            }

            function get_recipients_count() {

                let recipients_value = $get_recipients[0].value.trim();

                if (recipients_value) {
                    let delimiter = get_delimiter();

                    if (delimiter === ';') {
                        number_of_recipients_manual = recipients_value.split(';').length;
                    } else if (delimiter === ',') {
                        number_of_recipients_manual = recipients_value.split(',').length;
                    } else if (delimiter === '|') {
                        number_of_recipients_manual = recipients_value.split('|').length;
                    } else if (delimiter === 'tab') {
                        number_of_recipients_manual = recipients_value.split(' ').length;
                    } else if (delimiter === 'new_line') {
                        number_of_recipients_manual = recipients_value.split('\n').length;
                    } else {
                        number_of_recipients_manual = 0;
                    }
                } else {
                    number_of_recipients_manual = 0;
                }
                let total = number_of_recipients_manual + Number(number_of_recipients_ajax);

                $('.number_of_recipients').text(total);
            }

            $get_recipients.keyup(get_recipients_count);


            $("input[name='delimiter']").change(function () {
                get_recipients_count();
            });


            function get_character() {
                if ($get_msg[0].value !== null) {

                    let data = SmsCounter.count($get_msg[0].value, true);

                    if (data.encoding === 'UTF16') {
                        $('#sms_type').val('unicode').trigger('change');
                    } else {
                        $('#sms_type').val('plain').trigger('change');
                    }

                    $remaining.text(data.remaining + " {!! __('locale.labels.characters_remaining') !!}");
                    $messages.text(data.messages + " {!! __('locale.labels.message') !!}" + '(s)');

                }

            }


            merge_state.on('change', function () {
                const caretPos = $get_msg[0].selectionStart;
                const textAreaTxt = $get_msg.val();
                let txtToAdd = this.value;
                if (txtToAdd) {
                    txtToAdd = '{' + txtToAdd + '}';
                }

                $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));
            });


            $("#sms_template").on('change', function () {

                let template_id = $(this).val();

                $.ajax({
                    url: "{{ url('templates/show-data')}}" + '/' + template_id,
                    type: "POST",
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    cache: false,
                    success: function (data) {
                        if (data.status === 'success') {
                            const caretPos = $get_msg[0].selectionStart;
                            const textAreaTxt = $get_msg.val();
                            let txtToAdd = data.message;

                            $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos)).val().length;

                            get_character();

                        } else {
                            toastr['warning'](data.message, "{{ __('locale.labels.attention') }}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    },
                    error: function (reject) {
                        if (reject.status === 422) {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            });
                        } else {
                            toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        }
                    }
                });
            });

            $get_msg.keyup(get_character);

        });
    </script>
@endsection
