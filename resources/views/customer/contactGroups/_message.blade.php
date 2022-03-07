<div class="col-md-6 col-12">
    <form class="form form-vertical" action="{{ route('customer.contacts.message', $contact->uid) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="message_form" class="required">{{__('locale.labels.message_form')}}</label>
                    <select class="form-control select2" name="message_form" required id="message_form">
                        <option value="signup_sms">{{ __('locale.contacts.signup_sms') }}</option>
                        <option value="welcome_sms">{{ __('locale.contacts.welcome_message') }}</option>
                        <option value="unsubscribe_sms">{{ __('locale.contacts.unsubscribe_sms') }}</option>
                    </select>
                    @error('message_form')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
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
        </div>

        <div class="show-subscribe-url">
            <p class="m-0"><small class="text-uppercase text-primary"> {{__('locale.labels.subscribe_url')}} </small></p>
            <div class="row">
                <div class="col-md-10 col-sm-12 pr-0">
                    <div class="form-group">
                        <input type="text" class="form-control" id="copy-to-clipboard-input" value="{{route('contacts.subscribe_url', $contact->uid)}}">
                    </div>
                </div>
                <div class="col-md-2 col-sm-12">
                    <button type="button" class="btn btn-primary" id="btn-copy">{{__('locale.buttons.copy')}}!</button>
                </div>
            </div>
        </div>

        {{--   using on select message like sms template in ultimate sms     --}}
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="text_message" class="required">{{__('locale.labels.message')}}</label>
                    <textarea class="form-control" name="message" rows="5" id="text_message">{{$contact->signup_sms}}</textarea>
                    @error('message')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                </button>
            </div>
        </div>
    </form>
</div>
