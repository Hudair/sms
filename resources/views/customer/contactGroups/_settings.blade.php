<div class="col-md-6 col-12 settings">
    <form class="form form-vertical" action="{{ route('customer.contacts.update', $contact->uid) }}" method="post">
        {{ method_field('PUT') }}
        @csrf
        <div class="row">

            <div class="col-12">
                <div class="form-group">
                    <label for="name" class="required">{{__('locale.labels.name')}}</label>
                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $contact->name }}" name="name" required>
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
                    <div class="col-sm-6 col-12 mb-1">
                        <fieldset>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <div class="vs-radio-con">
                                            <input type="radio" name="originator" @if( !is_numeric($contact->sender_id) && !is_null($contact->sender_id)) checked @endif class="sender_id" value="sender_id">
                                            <span class="vs-radio vs-radio-sm">
                                          <span class="vs-radio--border"></span>
                                          <span class="vs-radio--circle"></span>
                                        </span>
                                        </div>

                                    </div>
                                    <select class="form-control select2" id="sender_id" name="sender_id" @if( is_numeric($contact->sender_id) || is_null($contact->sender_id)) disabled @endif >
                                        <option>{{ __('locale.labels.sender_id') }}</option>
                                        @foreach($sender_ids as $sender_id)
                                            <option value="{{$sender_id->sender_id}}" {{ $sender_id->sender_id == $contact->sender_id ? 'selected': null  }}> {{ $sender_id->sender_id }} </option>
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
                                            <input type="radio" name="originator" class="phone_number" @if( is_numeric($contact->sender_id)) checked @endif  value="phone_number">
                                            <span class="vs-radio vs-radio-sm">
                                          <span class="vs-radio--border"></span>
                                          <span class="vs-radio--circle"></span>
                                        </span>
                                        </div>
                                    </div>

                                    <select class="form-control select2" id="phone_number" name="phone_number" @if( !is_numeric($contact->sender_id)) disabled @endif >
                                        <option> {{ __('locale.labels.shared_number') }}</option>
                                        @foreach($phone_numbers as $number)
                                            <option value="{{ $number->number }}" {{ $number->number == $contact->sender_id ? 'selected': null  }}> {{ $number->number }} </option>
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
                        <input type="text" id="sender_id" class="form-control @error('sender_id') is-invalid @enderror" value="{{ $contact->sender_id }}" name="sender_id">
                        @error('sender_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            @endif

        </div>

        <div class="row">
            <div class="col-12">
                <div class="divider divider-left divider-primary">
                    <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.labels.settings') }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-10">
                <p class="mb-0 text-primary">{{ __('locale.contacts.send_welcome_message') }}?</p>
                <p><small class="">{!! __('locale.contacts.send_welcome_message_description') !!}</small></p>

            </div>
            <div class="col-2">
                <div class="form-group">
                    <div class="custom-control custom-switch switch-lg custom-switch-success mt-2">
                        <input type="checkbox" name="send_welcome_sms" value="1" {{ $contact->send_welcome_sms == 1 ? 'checked': null  }} class="custom-control-input" id="send_welcome_sms">
                        <label class="custom-control-label" for="send_welcome_sms">
                            <span class="switch-text-left">{{ __('locale.labels.yes') }}</span>
                            <span class="switch-text-right">{{ __('locale.labels.no') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-10">
                <p class="mb-0 text-primary">{{ __('locale.contacts.send_unsubscribe_notification') }}?</p>
                <p><small class="">{!! __('locale.contacts.send_unsubscribe_notification_description') !!}</small></p>

            </div>
            <div class="col-2">
                <div class="form-group">
                    <div class="custom-control custom-switch switch-lg custom-switch-success mt-2">
                        <input type="checkbox" name="unsubscribe_notification" value="1" {{ $contact->unsubscribe_notification == 1 ? 'checked': null  }}  class="custom-control-input" id="unsubscribe_notification">
                        <label class="custom-control-label" for="unsubscribe_notification">
                            <span class="switch-text-left">{{ __('locale.labels.yes') }}</span>
                            <span class="switch-text-right">{{ __('locale.labels.no') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @can('view_keywords')
            <div class="row mt-2">
                <div class="col-10">
                    <p class="mb-0 text-primary">{{ __('locale.contacts.send_keyword_message') }}?</p>
                    <p><small class="">{!! __('locale.contacts.send_keyword_message_description') !!}</small> <a href="#" class="text-danger small">{{ __('locale.menu.Keywords') }}</a></p>

                </div>
                <div class="col-2">
                    <div class="form-group">
                        <div class="custom-control custom-switch switch-lg custom-switch-success mt-2">
                            <input type="checkbox" name="send_keyword_message" value="1" {{ $contact->send_keyword_message == 1 ? 'checked': null  }}  class="custom-control-input" id="send_keyword_message">
                            <label class="custom-control-label" for="send_keyword_message">
                                <span class="switch-text-left">{{ __('locale.labels.yes') }}</span>
                                <span class="switch-text-right">{{ __('locale.labels.no') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        <div class="row mt-2">
            <div class="col-12">
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                </button>
            </div>
        </div>
    </form>
</div>
