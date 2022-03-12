<div class="row">
    <div class="col-12 me-1">
        <div class="card">
            <div class="card-body">
                <form class="form" action="{{ route('customer.subscriptions.preferences', $subscription->uid) }}" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="credit_warning"
                                       id="credit_warning"
                                       value="true"
                                        {{ $subscription->getOption('credit_warning') ? 'checked': null }}
                                >
                                <label class="form-check-label" for="credit_warning">{{ __('locale.labels.credit_warning') }}</label>
                            </div>
                            <p class="mx-4">{{ __('locale.subscription.credit_runs_bellow_description') }}</span></p>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="credit" class="form-label required">{{__('locale.subscription.credit_runs_bellow')}}</label>
                                    <input type="number" id="credit" class="form-control text-end @error('credit') is-invalid @enderror" value="{{ $subscription->getOption('credit') }}" name="credit" required>
                                    @error('credit')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="credit_notify" class="form-label required">{{ __('locale.labels.notify_me_by') }}</label>
                                    <select class="form-select" id="credit_notify" name="credit_notify">
                                        <option value="sms" {{ $subscription->getOption('credit_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                                        <option value="email" {{ $subscription->getOption('credit_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                                        <option value="both" {{ $subscription->getOption('credit_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                                    </select>
                                </div>
                                @error('credit_notify')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="subscription_warning"
                                       id="subscription_warning"
                                       value="true"
                                        {{ $subscription->getOption('subscription_warning') ? 'checked': null }}
                                >
                                <label class="form-check-label" for="subscription_warning">{{ __('locale.labels.subscription_warning') }}</label>
                            </div>
                            <p class="mx-4">{{ __('locale.subscription.subscription_warning_description') }}</p>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="end_period_last_days" class="form-label required">{{ __('locale.subscription.subscription_period_end') }}</label>
                                    <input type="number" id="end_period_last_days" class="form-control text-end @error('end_period_last_days') is-invalid @enderror" value="{{ $subscription->end_period_last_days }}" name="end_period_last_days" required>
                                    @error('end_period_last_days')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="subscription_notify" class="form-label required">{{ __('locale.labels.notify_me_by') }}</label>
                                    <select class="form-select" id="subscription_notify" name="subscription_notify">
                                        <option value="sms" {{ $subscription->getOption('subscription_notify') == 'sms' ? 'selected': null }}> {{__('locale.labels.sms')}}</option>
                                        <option value="email" {{ $subscription->getOption('subscription_notify') == 'email' ? 'selected': null }}>  {{__('locale.labels.email')}}</option>
                                        <option value="both" {{ $subscription->getOption('subscription_notify') == 'both' ? 'selected': null }}>  {{__('locale.labels.both')}}</option>
                                    </select>
                                </div>
                                @error('credit_notify')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                        <button type="submit" class="btn btn-primary"><i data-feather="save"></i> {{__('locale.buttons.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
