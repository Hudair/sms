<div class="row ml-1">
    <div class="col-12">
        <form class="form form-vertical" action="{{ route('customer.subscriptions.preferences', $subscription->uid) }}" method="post">
            @csrf

            <div class="row">
                <div class="col-8">
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox"
                               class="custom-control-input"
                               name="credit_warning"
                               id="credit_warning"
                               value="true"
                                {{ $subscription->getOption('credit_warning') ? 'checked': null }}
                        >
                        <label class="custom-control-label" for="credit_warning"></label>
                        <span class="switch-label">{{ __('locale.labels.credit_warning') }}</span>
                    </div>
                    <p class="ml-5">{{ __('locale.subscription.credit_runs_bellow_description') }}</p>
                </div>

                <div class="col-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="credit" class="required">{{__('locale.subscription.credit_runs_bellow')}}</label>
                            <input type="number" id="credit" class="form-control text-right @error('credit') is-invalid @enderror" value="{{ $subscription->getOption('credit') }}" name="credit" required>
                            @error('credit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="credit_notify" class="required">{{ __('locale.labels.notify_me_by') }}</label>
                            <select class="form-control" id="credit_notify" name="credit_notify">
                                <option value="sms" {{ $subscription->getOption('credit_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                                <option value="email" {{ $subscription->getOption('credit_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                                <option value="both" {{ $subscription->getOption('credit_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                            </select>
                        </div>
                        @error('credit_notify')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-8">
                    <div class="custom-control custom-switch custom-control-inline">
                        <input type="checkbox"
                               class="custom-control-input"
                               name="subscription_warning"
                               id="subscription_warning"
                               value="true"
                                {{ $subscription->getOption('subscription_warning') ? 'checked': null }}
                        >
                        <label class="custom-control-label" for="subscription_warning">
                        </label>
                        <span class="switch-label">{{ __('locale.labels.subscription_warning') }}</span>
                    </div>
                    <p class="ml-5">{{ __('locale.subscription.subscription_warning_description') }}</p>
                </div>

                <div class="col-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="end_period_last_days" class="required">{{ __('locale.subscription.subscription_period_end') }}</label>
                            <input type="number" id="end_period_last_days" class="form-control text-right @error('end_period_last_days') is-invalid @enderror" value="{{ $subscription->end_period_last_days }}" name="end_period_last_days" required>
                            @error('end_period_last_days')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="subscription_notify" class="required">{{ __('locale.labels.notify_me_by') }}</label>
                            <select class="form-control" id="subscription_notify" name="subscription_notify">
                                <option value="sms" {{ $subscription->getOption('subscription_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                                <option value="email" {{ $subscription->getOption('subscription_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                                <option value="both" {{ $subscription->getOption('subscription_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                            </select>
                        </div>
                        @error('credit_notify')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary mr-1 mb-1 pull-right">
                        <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
