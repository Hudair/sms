<div class="row">

    <div class="card">
        <div class="card-body">
            <div class="col-md-7 col-12 settings">
                <form class="form form-vertical" action="{{ route('customer.contacts.update', $contact->uid) }}" method="post">
                    {{ method_field('PUT') }}
                    @csrf
                    <div class="row">

                        <div class="col-12">
                            <div class="mb-1">
                                <label for="name" class="form-label required">{{__('locale.labels.name')}}</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $contact->name }}" name="name" required>
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <label for="sending_server" class="form-label required">{{ __('locale.labels.sending_server') }}</label>
                                <select class="select2 form-select" name="sending_server">
                                    @foreach($sending_server as $server)
                                        <option value="{{$server->id}}" {{ $contact->sending_server == $server->id ? 'selected' : null }}> {{ $server->name }}</option>
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

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="divider divider-left divider-primary">
                                <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.labels.settings') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-10">
                            <p class="mb-0 text-primary">{{ __('locale.contacts.send_welcome_message') }}?</p>
                            <p><small class="">{!! __('locale.contacts.send_welcome_message_description') !!}</small></p>
                        </div>

                        <div class="col-2">
                            <div class="mb-1">
                                <div class="form-check form-switch form-check-primary form-switch-md">
                                    <input type="checkbox" name="send_welcome_sms" value="1" {{ $contact->send_welcome_sms == 1 ? 'checked': null  }} class="form-check-input" id="send_welcome_sms">
                                    <label class="form-check-label" for="send_welcome_sms">
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
                            <div class="mb-1">
                                <div class="form-check form-switch form-check-primary form-switch-md">
                                    <input type="checkbox" name="unsubscribe_notification" value="1" {{ $contact->unsubscribe_notification == 1 ? 'checked': null  }}  class="form-check-input" id="unsubscribe_notification">
                                    <label class="form-check-label" for="unsubscribe_notification">
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
                                <div class="mb-1">
                                    <div class="form-check form-switch form-check-primary form-switch-md">
                                        <input type="checkbox" name="send_keyword_message" value="1" {{ $contact->send_keyword_message == 1 ? 'checked': null  }}  class="form-check-input" id="send_keyword_message">
                                        <label class="form-check-label" for="send_keyword_message">
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
                            <button type="submit" class="btn btn-primary mb-1">
                                <i data-feather="save"></i> {{__('locale.buttons.save')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
