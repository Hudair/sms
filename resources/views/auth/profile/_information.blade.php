<div class="card">
    <div class="card-body py-2 my-25">
        <form class="form form-vertical" action="{{ route('user.account.update_information') }}" method="post">
            @csrf
            <div class="row mt-1">
                <div class="col-12 col-sm-6">
                    <h5 class="mb-1"><i data-feather="user"></i>{{__('locale.customer.personal_information')}}</h5>

                    <div class="mb-1">
                        <label for="phone" class="form-label required">{{__('locale.labels.phone')}}</label>
                        <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $user->customer->phone }}" name="phone" required>
                        @error('phone')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="company" class="form-label">{{__('locale.labels.company')}}</label>
                        <input type="text" id="company" class="form-control @error('company') is-invalid @enderror" value="{{ $user->customer->company }}" name="company">
                        @error('company')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="website" class="form-label">{{__('locale.labels.website')}}</label>
                        <input type="url" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ $user->customer->website }}" name="website">
                        @error('website')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <h5 class="mb-1 mt-2 mt-sm-0"><i data-feather="map-pin"></i> {{__('locale.labels.address')}}</h5>

                    <div class="mb-1">
                        <label for="address" class="form-label required">{{__('locale.labels.address')}}</label>
                        <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ $user->customer->address }}" name="address" required>
                        @error('address')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="city" class="form-label required">{{__('locale.labels.city')}}</label>
                        <input type="text" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ $user->customer->city }}" name="city" required>
                        @error('city')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="state" class="form-label">{{__('locale.labels.state')}}</label>
                        <input type="text" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ $user->customer->state }}" name="state">
                        @error('state')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="postcode" class="form-label">{{__('locale.labels.postcode')}}</label>
                        <input type="text" id="postcode" class="form-control @error('postcode') is-invalid @enderror" value="{{ $user->customer->postcode }}" name="postcode">
                        @error('postcode')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="country" class="form-label required">{{__('locale.labels.country')}}</label>
                        <select class="form-select select2" id="country" name="country">
                            @foreach(\App\Helpers\Helper::countries() as $country)
                                <option value="{{$country['name']}}" {{ $user->customer->country == $country['name'] ? 'selected': null }}> {{ $country['name'] }}</option>
                            @endforeach
                        </select>
                        @error('country')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>


                </div>
                <div class="col-12 col-sm-6">

                    <h5 class="mb-1 mt-2 mt-sm-0"><i data-feather="map-pin"></i> {{ __('locale.labels.billing_address') }}</h5>


                    <div class="mb-1">
                        <label for="financial_address" class="form-label">{{__('locale.labels.address')}}</label>
                        <input type="text" id="financial_address" class="form-control @error('financial_address') is-invalid @enderror" value="{{ $user->customer->financial_address }}" name="financial_address">
                        @error('financial_address')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="financial_city" class="form-label">{{__('locale.labels.city')}}</label>
                        <input type="text" id="financial_city" class="form-control @error('financial_city') is-invalid @enderror" value="{{ $user->customer->financial_city }}" name="financial_city">
                        @error('financial_city')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="financial_postcode" class="form-label">{{__('locale.labels.postcode')}}</label>
                        <input type="text" id="financial_postcode" class="form-control @error('financial_postcode') is-invalid @enderror" value="{{ $user->customer->financial_postcode }}" name="financial_postcode">
                        @error('financial_postcode')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label for="tax_number" class="form-label">{{__('locale.labels.tax_number')}}</label>
                        <input type="text" id="tax_number" class="form-control @error('tax_number') is-invalid @enderror" value="{{ $user->customer->tax_number }}" name="tax_number">
                        @error('tax_number')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>


                    <h5 class="mb-1 mt-2 mt-sm-0"><i data-feather="alert-circle"></i> {{__('locale.labels.email_notifications')}}</h5>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="login" class="mr-2">{{__('locale.auth.login')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{__('locale.customer.every_single_login')}}'></i> </label>
                            <input type="checkbox" name="notifications[login]" value="yes" class="form-check-input" id="login" {{ $user->customer->getNotifications()['login'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="login">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="sender_id" class="mr-1">{{__('locale.labels.sender_id')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{__('locale.customer.sender_id_verification')}}'></i> </label>
                            <input type="checkbox" name="notifications[sender_id]" value="yes" class="form-check-input" id="sender_id" {{ $user->customer->getNotifications()['sender_id'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="sender_id">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="keyword" class="mr-1">{{__('locale.labels.keyword')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{__('locale.customer.purchase_keyword')}}'></i> </label>
                            <input type="checkbox" name="notifications[keyword]" value="yes" class="form-check-input" id="keyword" {{ $user->customer->getNotifications()['keyword'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="keyword">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="subscription" class="mr-1">{{__('locale.menu.Subscriptions')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{__('locale.customer.successful_subscription')}}'></i> </label>
                            <input type="checkbox" class="form-check-input" name="notifications[subscription]" value="yes" id="subscription" {{ $user->customer->getNotifications()['subscription'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="subscription">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="promotion" class="mr-1">{{__('locale.labels.promotion')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{__('locale.customer.promotional_newsletter')}}'></i> </label>
                            <input type="checkbox" class="form-check-input" name="notifications[promotion]" value="yes" id="promotion" {{ $user->customer->getNotifications()['promotion'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="promotion">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-1">
                        <div class="form-check form-switch form-check-primary">
                            <label for="profile" class="mr-1">{{__('locale.labels.profile')}} <i data-feather="help-circle" data-bs-toggle='tooltip' data-bs-placement='top' title='{{ __('locale.customer.profile_activities') }}'></i> </label>
                            <input type="checkbox" class="form-check-input" id="profile" name="notifications[profile]" value="yes" {{ $user->customer->getNotifications()['profile'] == 'yes' ? 'checked':null }}>
                            <label class="form-check-label" for="profile">
                                <span class="switch-icon-left"><i data-feather="check"></i> </span>
                                <span class="switch-icon-right"><i data-feather="x"></i> </span>
                            </label>
                        </div>
                    </div>

                </div>
                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i data-feather="save"></i> {{ __('locale.buttons.save_changes') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
