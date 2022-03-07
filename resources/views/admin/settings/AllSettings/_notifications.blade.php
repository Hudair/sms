<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.notifications') }}" method="post">
            @csrf
            <div class="col-12">
                <div class="form-group">
                    <label for="notification_sms_gateway" class="required">{{__('locale.settings.notification_sms_gateway')}}</label>
                    <select class="form-control select2" id="notification_sms_gateway" name="notification_sms_gateway">
                        @if($sending_servers->count())
                            @foreach($sending_servers as $server)
                                <option value="{{$server->uid}}" @if($server->uid == \App\Helpers\Helper::app_config('notification_sms_gateway')) selected @endif > {{ $server->name }}</option>
                            @endforeach
                        @else
                            <option>{{ __('locale.sending_servers.have_no_sending_server_to_add') }}</option>
                        @endif
                    </select>
                    @error('notification_sms_gateway')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="notification_sender_id" class="required">{{ __('locale.settings.notification_sender_id') }}</label>
                    <input type="text" id="notification_sender_id" name="notification_sender_id" required class="form-control" value="{{ \App\Helpers\Helper::app_config('notification_sender_id') }}">
                    @error('notification_sender_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="notification_phone" class="required">{{ __('locale.settings.notification_phone') }}</label>
                    <input type="text" id="notification_phone" name="notification_phone" required class="form-control" value="{{ \App\Helpers\Helper::app_config('notification_phone') }}">
                    @error('notification_phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="notification_from_name" class="required">{{ __('locale.settings.notification_from_name') }}</label>
                    <input type="text" id="notification_from_name" name="notification_from_name" required class="form-control" value="{{ \App\Helpers\Helper::app_config('notification_from_name') }}">
                    @error('notification_from_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="notification_email" class="required">{{ __('locale.settings.notification_email') }}</label>
                    <input type="text" id="notification_email" name="notification_email" required class="form-control" value="{{ \App\Helpers\Helper::app_config('notification_email') }}">
                    @error('notification_email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            {{-- Sender id notification --}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="sender_id_notification_email" {{ \App\Helpers\Helper::app_config('sender_id_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.sender_id_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="sender_id_notification_sms" {{ \App\Helpers\Helper::app_config('sender_id_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.sender_id_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            {{--User Registration notificaiton--}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="user_registration_notification_email" {{ \App\Helpers\Helper::app_config('user_registration_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.user_registration_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="user_registration_notification_sms" {{ \App\Helpers\Helper::app_config('user_registration_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.user_registration_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            {{--Subscription notificaiton--}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="subscription_notification_email" {{ \App\Helpers\Helper::app_config('subscription_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.subscription_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="subscription_notification_sms" {{ \App\Helpers\Helper::app_config('subscription_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.subscription_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            {{--keywords notificaiton--}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="keyword_notification_email" {{ \App\Helpers\Helper::app_config('keyword_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.keyword_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="keyword_notification_sms" {{ \App\Helpers\Helper::app_config('keyword_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.keyword_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            {{-- purchase number notification --}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="phone_number_notification_email" {{ \App\Helpers\Helper::app_config('phone_number_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.phone_number_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="phone_number_notification_sms" {{ \App\Helpers\Helper::app_config('phone_number_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.phone_number_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            {{-- block message notification --}}
            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="block_message_notification_email" {{ \App\Helpers\Helper::app_config('block_message_notification_email') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.block_message_notification_email')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12">
                <fieldset>
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" value="true" name="block_message_notification_sms" {{ \App\Helpers\Helper::app_config('block_message_notification_sms') == true ? 'checked': null }}>
                        <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        <span class="">{{__('locale.settings.block_message_notification_sms')}}</span>
                    </div>

                </fieldset>
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                </button>
            </div>


        </form>
    </div>
</div>
