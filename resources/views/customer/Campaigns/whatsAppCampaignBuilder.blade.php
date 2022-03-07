@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Campaign Builder'))

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
        .btn-group, .btn-group-vertical {
            display: inline-table !important;
        }

        .recipients input[type=radio] {
            box-sizing: border-box;
            padding: 0;
            position: absolute;
            pointer-events: none;
            clip: rect(0, 0, 0, 0);
        }

        .recipients label.active.btn {
            color: #ffffff !important;
            background-color: #7E57C2 !important;
            border-color: #7E57C2 !important;
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

                            <form class="form form-vertical" action="{{ route('customer.whatsapp.campaign_builder') }}" method="post">
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name"
                                                       class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       value="{{ old('name') }}" name="name" required
                                                       placeholder="{{__('locale.labels.required')}}" autofocus>
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
                                                <div class="col-md-6 col-12 customized_select2">
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
                                                <div class="col-md-6 col-12 customized_select2">
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
                                                <label for="contact_groups">{{ __('locale.contacts.contact_groups') }}</label>
                                                <select class="select2 form-control" name="contact_groups[]"
                                                        multiple="multiple">
                                                    @foreach($contact_groups as $group)
                                                        <option value="{{$group->id}}"> {{ $group->name }}
                                                            ({{\App\Library\Tool::number_with_delimiter($group->subscribersCount($group->cache))}} {{__('locale.menu.Contacts')}}
                                                            )
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('contact_groups')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">

                                            <div class="form-group">
                                                <label for="recipients">{{ __('locale.labels.manual_input') }}</label>
                                                <small class="text-uppercase pull-right">{{ __('locale.labels.total_number_of_recipients') }}
                                                    :<span class="number_of_recipients bold text-success m-r-5">0</span></small>
                                                <textarea class="form-control" id="recipients" name="recipients"></textarea>
                                                @error('recipients')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="delimiter">{{ __('locale.labels.choose_delimiter') }}</label><br>
                                                <div class="btn-group btn-group-sm recipients" data-toggle="buttons">

                                                    <label class="btn btn-outline-primary active">
                                                        <input type="radio" name="delimiter" value="," checked>,
                                                        ({{ __('locale.labels.comma') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value=";">;
                                                        ({{ __('locale.labels.semicolon') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter" value="|">|
                                                        ({{ __('locale.labels.bar') }})
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter"
                                                               value="tab">{{__('locale.labels.tab')}}
                                                    </label>

                                                    <label class="btn btn-outline-primary">
                                                        <input type="radio" name="delimiter"
                                                               value="new_line">{{ __('locale.labels.new_line') }}
                                                    </label>

                                                    @error('delimiter')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror

                                                </div>
                                            </div>
                                            <p>
                                                <small class="text-primary">{!! __('locale.description.manual_input') !!} {!! __('locale.contacts.include_country_code_for_successful_import') !!}</small>
                                            </p>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="sms_template">{{__('locale.permission.sms_template')}}</label>
                                                <select class="form-control select2" id="sms_template">
                                                    <option>{{ __('locale.labels.select_one') }}</option>
                                                    @foreach($templates as $template)
                                                        <option value="{{$template->id}}">{{ $template->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>{{__('locale.labels.available_tag')}}</label>
                                                <select class="form-control select2" id="available_tag">
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
                                            <div class="form-group">
                                                <label for="message"
                                                       class="required">{{__('locale.labels.message')}}</label>
                                                <textarea class="form-control" name="message" rows="5"
                                                          id="message"></textarea>
                                                <small class="text-primary text-uppercase"
                                                       id="remaining">160 {{ __('locale.labels.characters_remaining') }}</small>
                                                <small class="text-primary text-uppercase pull-right"
                                                       id="messages">1 {{ __('locale.labels.message') }} (s)</small>
                                                @error('message')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" class="schedule" value="true"
                                                           name="schedule" {{ old('schedule') == true ? "checked" : null }}>
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.campaigns.schedule_campaign')}}?</span>
                                                </div>
                                                <p>
                                                    <small class="text-muted">{{__('locale.campaigns.schedule_campaign_note')}}</small>
                                                </p>

                                            </div>
                                        </div>

                                    </div>

                                    <div class="row schedule_time">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="schedule_date">{{ __('locale.labels.date') }}</label>
                                                <input type="text" id="schedule_date" name="schedule_date"
                                                       class="form-control schedule_date" placeholder="YYYY-MM-DD"/>
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
                                                <input type="text" id="time"
                                                       class="form-control flatpickr-time text-left"
                                                       name="schedule_time" placeholder="HH:MM"/>
                                                @error('schedule_time')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

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

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="frequency_cycle">{{__('locale.labels.frequency')}}</label>
                                                <select class="form-control" id="frequency_cycle"
                                                        name="frequency_cycle">
                                                    <option value="onetime" {{old('frequency_cycle')}}> {{__('locale.labels.one_time')}}</option>
                                                    <option value="daily" {{old('frequency_cycle')}}> {{__('locale.labels.daily')}}</option>
                                                    <option value="monthly" {{old('frequency_cycle')}}> {{__('locale.labels.monthly')}}</option>
                                                    <option value="yearly" {{old('frequency_cycle')}}> {{__('locale.labels.yearly')}}</option>
                                                    <option value="custom" {{old('frequency_cycle')}}> {{__('locale.labels.custom')}}</option>
                                                </select>
                                            </fieldset>
                                            @error('frequency_cycle')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <div class="form-group">
                                                <label for="frequency_amount">{{__('locale.plans.frequency_amount')}}</label>
                                                <input type="text"
                                                       id="frequency_amount"
                                                       class="form-control text-right @error('frequency_amount') is-invalid @enderror"
                                                       name="frequency_amount"
                                                       value="{{ old('frequency_amount') }}"
                                                >
                                                @error('frequency_amount')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-12 show-custom">
                                            <fieldset class="form-group">
                                                <label for="frequency_unit">{{__('locale.plans.frequency_unit')}}</label>
                                                <select class="form-control" id="frequency_unit" name="frequency_unit">
                                                    <option value="day" {{old('frequency_unit')}}> {{__('locale.labels.day')}}</option>
                                                    <option value="week" {{old('frequency_unit')}}> {{__('locale.labels.week')}}</option>
                                                    <option value="month" {{old('frequency_unit')}}> {{__('locale.labels.month')}}</option>
                                                    <option value="year" {{old('frequency_unit')}}> {{__('locale.labels.year')}}</option>
                                                </select>
                                            </fieldset>
                                            @error('frequency_unit')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 show-recurring">
                                            <div class="form-group">
                                                <label for="recurring_date"> {{ __('locale.labels.end_date') }}</label>
                                                <input type="text" id="recurring_date" name="recurring_date"
                                                       class="form-control schedule_date" placeholder="YYYY-MM-DD"/>
                                                @error('recurring_date')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 show-recurring">
                                            <div class="form-group">
                                                <label for="recurring_time">{{ __('locale.labels.end_time') }}</label>
                                                <input type="text" id="recurring_time"
                                                       class="form-control flatpickr-time text-left"
                                                       name="recurring_time" placeholder="HH:MM"/>
                                                @error('recurring_time')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="advanced" class="advanced"
                                                           value="true">
                                                    <span class="vs-checkbox"><span class="vs-checkbox--check"><i
                                                                    class="vs-icon feather icon-check"></i></span></span>
                                                    <span class="">{{ __('locale.labels.advanced') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row advanced_div">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" value="true" name="send_copy">
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.campaigns.send_copy_via_email')}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" value="true" name="create_template">
                                                    <span class="vs-checkbox">
                                                          <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                          </span>
                                                        </span>
                                                    <span class="">{{__('locale.campaigns.create_template_based_message')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <input type="hidden" value="whatsapp" name="sms_type">
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

            let number_of_recipients_ajax = 0,
                number_of_recipients_manual = 0,
                $get_recipients = $('#recipients'),
                $remaining = $('#remaining'),
                $messages = $remaining.next(),
                maxCharInitial = 160,
                maxChar = 157,
                messages = 1,
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
                            toastr.warning(data.message, "{{__('locale.labels.attention')}}", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        }
                    },
                    error: function (reject) {
                        if (reject.status === 422) {
                            let errors = reject.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                    positionClass: 'toast-top-right',
                                    containerId: 'toast-top-right',
                                    progressBar: true,
                                    closeButton: true,
                                    newestOnTop: true
                                });
                            });
                        } else {
                            toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        }
                    }
                });
            });

            $get_msg.keyup(get_character);

        });
    </script>
@endsection
