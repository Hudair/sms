<form class="form form-vertical" action="{{ route('admin.customers.update_information', $customer->uid) }}" method="post">
    @csrf
    <div class="row mt-1">
        <div class="col-12 col-sm-6">
            <h5 class="mb-1"><i class="feather icon-user mr-25"></i>{{__('locale.customer.personal_information')}}</h5>

            <div class="form-group">
                <div class="controls">
                    <label for="phone" class="required">{{__('locale.labels.phone')}}</label>
                    <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $customer->customer->phone }}" name="phone" required>
                    @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="company">{{__('locale.labels.company')}}</label>
                    <input type="text" id="company" class="form-control @error('company') is-invalid @enderror" value="{{ $customer->customer->company }}" name="company">
                    @error('company')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="website">{{__('locale.labels.website')}}</label>
                    <input type="url" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ $customer->customer->website }}" name="website">
                    @error('website')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <h5 class="mb-1 mt-2 mt-sm-0"><i class="feather icon-map-pin mr-25"></i>{{__('locale.labels.address')}}</h5>

            <div class="form-group">
                <div class="controls">
                    <label for="address" class="required">{{__('locale.labels.address')}}</label>
                    <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ $customer->customer->address }}" name="address" required>
                    @error('address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="city" class="required">{{__('locale.labels.city')}}</label>
                    <input type="text" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ $customer->customer->city }}" name="city" required>
                    @error('city')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="state">{{__('locale.labels.state')}}</label>
                    <input type="text" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ $customer->customer->state }}" name="state">
                    @error('state')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="postcode">{{__('locale.labels.postcode')}}</label>
                    <input type="text" id="postcode" class="form-control @error('postcode') is-invalid @enderror" value="{{ $customer->customer->postcode }}" name="postcode">
                    @error('postcode')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="country" class="required">{{__('locale.labels.country')}}</label>
                    <select class="form-control select2" id="country" name="country">
                        @foreach(\App\Helpers\Helper::countries() as $country)
                            <option value="{{$country['name']}}" {{ $customer->customer->country == $country['name'] ? 'selected': null }}> {{ $country['name'] }}</option>
                        @endforeach
                    </select>
                    @error('country')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>


        </div>
        <div class="col-12 col-sm-6">

            <h5 class="mb-1 mt-2 mt-sm-0"><i class="feather icon-map-pin mr-25"></i> {{ __('locale.labels.billing_address') }}</h5>


            <div class="form-group">
                <div class="controls">
                    <label for="financial_address">{{__('locale.labels.address')}}</label>
                    <input type="text" id="financial_address" class="form-control @error('financial_address') is-invalid @enderror" value="{{ $customer->customer->financial_address }}" name="financial_address">
                    @error('financial_address')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="financial_city">{{__('locale.labels.city')}}</label>
                    <input type="text" id="financial_city" class="form-control @error('financial_city') is-invalid @enderror" value="{{ $customer->customer->financial_city }}" name="financial_city">
                    @error('financial_city')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="financial_postcode">{{__('locale.labels.postcode')}}</label>
                    <input type="text" id="financial_postcode" class="form-control @error('financial_postcode') is-invalid @enderror" value="{{ $customer->customer->financial_postcode }}" name="financial_postcode">
                    @error('financial_postcode')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="controls">
                    <label for="tax_number">{{__('locale.labels.tax_number')}}</label>
                    <input type="text" id="tax_number" class="form-control @error('tax_number') is-invalid @enderror" value="{{ $customer->customer->tax_number }}" name="tax_number">
                    @error('tax_number')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>


            <h5 class="mb-1 mt-2 mt-sm-0"><i class="feather icon-alert-circle mr-25"></i> {{__('locale.labels.email_notifications')}}</h5>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="login" class="mr-2">{{__('locale.auth.login')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{__('locale.customer.every_single_login')}}'></i> </label>
                    <input type="checkbox" name="notifications[login]" value="yes" class="custom-control-input" id="login" {{ $customer->customer->getNotifications()['login'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="login">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="sender_id" class="mr-1">{{__('locale.labels.sender_id')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{__('locale.customer.sender_id_verification')}}'></i> </label>
                    <input type="checkbox" name="notifications[sender_id]" value="yes" class="custom-control-input" id="sender_id" {{ $customer->customer->getNotifications()['sender_id'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="sender_id">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="keyword" class="mr-1">{{__('locale.labels.keyword')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{__('locale.customer.purchase_keyword')}}'></i> </label>
                    <input type="checkbox" name="notifications[keyword]" value="yes" class="custom-control-input" id="keyword" {{ $customer->customer->getNotifications()['keyword'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="keyword">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="subscription" class="mr-1">{{__('locale.menu.Subscriptions')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{__('locale.customer.successful_subscription')}}'></i> </label>
                    <input type="checkbox" class="custom-control-input" name="notifications[subscription]" value="yes" id="subscription" {{ $customer->customer->getNotifications()['subscription'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="subscription">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="promotion" class="mr-1">{{__('locale.labels.promotion')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{__('locale.customer.promotional_newsletter')}}'></i> </label>
                    <input type="checkbox" class="custom-control-input" name="notifications[promotion]" value="yes" id="promotion" {{ $customer->customer->getNotifications()['promotion'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="promotion">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control switch-md custom-switch custom-switch-primary">
                    <label for="profile" class="mr-1">{{__('locale.labels.profile')}} <i class="feather icon-help-circle text-primary" data-toggle='tooltip' data-placement='top' title='{{ __('locale.customer.profile_activities') }}'></i> </label>
                    <input type="checkbox" class="custom-control-input" id="profile" name="notifications[profile]" value="yes" {{ $customer->customer->getNotifications()['profile'] == 'yes' ? 'checked':null }}>
                    <label class="custom-control-label" for="profile">
                        <span class="switch-text-left">{{__('locale.labels.on')}}</span>
                        <span class="switch-text-right">{{__('locale.labels.off')}}</span>
                    </label>
                </div>
            </div>

        </div>
        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
            <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save_changes') }}</button>
        </div>
    </div>
</form>
