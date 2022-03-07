<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.authentication') }}" method="post">
            @csrf
            <div class="row">

                <div class="col-12">
                    <div class="form-group">
                        <label for="client_registration" class="required">{{__('locale.settings.client_registration')}}</label>
                        <select class="form-control" id="client_registration" name="client_registration">
                            <option value="1" @if(config('account.can_register') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('account.can_register') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('client_registration')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="registration_verification" class="required">{{__('locale.settings.registration_verification')}}</label>
                        <select class="form-control" id="registration_verification" name="registration_verification">
                            <option value="1" @if(config('account.verify_account') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('account.verify_account') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('registration_verification')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="two_factor" class="required">{{__('locale.settings.two_factor_authentication')}}</label>
                        <select class="form-control" id="two_factor" name="two_factor">
                            <option value="1" @if(config('app.two_factor') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('app.two_factor') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('two_factor')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12 show-two-factor">
                    <input type="hidden" value="email" name="two_factor_send_by">
                </div>

                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-3">
                        <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.settings.recaptcha_information') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <p>{!! __('locale.description.captcha') !!} </p>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="captcha_site_key">{{ __('locale.settings.recaptcha_site_key') }}</label>
                        <input type="text" id="captcha_site_key" name="captcha_site_key" class="form-control" value="{{ config('no-captcha.sitekey') }}">
                        @error('captcha_site_key')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="captcha_secret_key">{{ __('locale.settings.recaptcha_secret_key') }}</label>
                        <input type="text" id="captcha_secret_key" name="captcha_secret_key" class="form-control" value="{{ config('no-captcha.secret') }}">
                        @error('captcha_secret_key')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="captcha_in_login" class="required">{{__('locale.settings.captcha_in_login')}}</label>
                        <select class="form-control" id="captcha_in_login" name="captcha_in_login">
                            <option value="1" @if(config('no-captcha.login') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('no-captcha.login') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('captcha_in_login')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="captcha_in_client_registration" class="required">{{__('locale.settings.captcha_in_client_registration')}}</label>
                        <select class="form-control" id="captcha_in_client_registration" name="captcha_in_client_registration">
                            <option value="1" @if(config('no-captcha.registration') == '1') selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('no-captcha.registration') == '0') selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('captcha_in_client_registration')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase text-bold-600 text-primary">
                            {{ __('locale.settings.facebook_login') }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="login_with_facebook">{{__('locale.settings.login_with_facebook')}}</label>
                        <select class="form-control" id="login_with_facebook" name="login_with_facebook">
                            <option value="1" @if(config('services.facebook.active') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('services.facebook.active') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_facebook')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12 show-facebook">
                    <p>{!! __('locale.description.facebook', ['brandname' => config('app.name'), 'callback_url' => route('social.callback', ['provider' => 'facebook'])] ) !!} </p>
                </div>

                <div class="col-12 show-facebook">
                    <div class="form-group">
                        <label for="facebook_client_id">{{ __('locale.settings.app_id') }}</label>
                        <input type="text" id="facebook_client_id" name="facebook_client_id" class="form-control" value="{{ config('services.facebook.client_id') }}">
                        @error('facebook_client_id')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-facebook">
                    <div class="form-group">
                        <label for="facebook_client_secret">{{ __('locale.settings.app_secret') }}</label>
                        <input type="text" id="facebook_client_secret" name="facebook_client_secret" class="form-control" value="{{ config('services.facebook.client_secret') }}">
                        @error('facebook_client_secret')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.settings.twitter_login') }}</div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="login_with_twitter">{{__('locale.settings.login_with_twitter')}}</label>
                        <select class="form-control" id="login_with_twitter" name="login_with_twitter">
                            <option value="1" @if(config('services.twitter.active') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('services.twitter.active') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_twitter')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12 show-twitter">
                    <p>{!! __('locale.description.twitter', ['url' => config('app.url'), 'callback_url' => route('social.callback', ['provider' => 'twitter'])] ) !!} </p>
                </div>

                <div class="col-12 show-twitter">
                    <div class="form-group">
                        <label for="twitter_client_id">{{ __('locale.labels.api_key') }}</label>
                        <input type="text" id="twitter_client_id" name="twitter_client_id" class="form-control" value="{{ config('services.twitter.client_id') }}">
                        @error('twitter_client_id')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-twitter">
                    <div class="form-group">
                        <label for="twitter_client_secret">{{ __('locale.labels.secret_key') }}</label>
                        <input type="text" id="twitter_client_secret" name="twitter_client_secret" class="form-control" value="{{ config('services.twitter.client_secret') }}">
                        @error('twitter_client_secret')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.settings.google_login') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="login_with_google">{{__('locale.settings.login_with_google')}}</label>
                        <select class="form-control" id="login_with_google" name="login_with_google">
                            <option value="1" @if(config('services.google.active') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('services.google.active') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_google')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12 show-google">
                    <p>{!! __('locale.description.google', ['callback_url' => route('social.callback', ['provider' => 'google'])] )!!} </p>
                </div>

                <div class="col-12 show-google">
                    <div class="form-group">
                        <label for="google_client_id">{{ __('locale.labels.client_id') }}</label>
                        <input type="text" id="google_client_id" name="google_client_id" class="form-control" value="{{ config('services.google.client_id') }}">
                        @error('google_client_id')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-google">
                    <div class="form-group">
                        <label for="google_client_secret">{{ __('locale.labels.client_secret') }}</label>
                        <input type="text" id="google_client_secret" name="google_client_secret" class="form-control" value="{{ config('services.google.client_secret') }}">
                        @error('google_client_secret')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="divider divider-left divider-primary mt-2">
                        <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.settings.github_login') }}</div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="login_with_github">{{__('locale.settings.login_with_github')}}</label>
                        <select class="form-control" id="login_with_github" name="login_with_github">
                            <option value="1" @if(config('services.github.active') == true) selected @endif>{{__('locale.labels.yes')}}</option>
                            <option value="0" @if(config('services.github.active') == false) selected @endif>{{__('locale.labels.no')}}</option>
                        </select>
                    </div>
                    @error('login_with_github')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12 show-github">
                    <p>{!! __('locale.description.github', ['url' => config('app.url'), 'callback_url' => route('social.callback', ['provider' => 'github'])] )!!} </p>
                </div>

                <div class="col-12 show-github">
                    <div class="form-group">
                        <label for="github_client_id">{{ __('locale.labels.client_id') }}</label>
                        <input type="text" id="github_client_id" name="github_client_id" class="form-control" value="{{ config('services.github.client_id') }}">
                        @error('github_client_id')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 show-github">
                    <div class="form-group">
                        <label for="github_client_secret">{{ __('locale.labels.client_secret') }}</label>
                        <input type="text" id="github_client_secret" name="github_client_secret" class="form-control" value="{{ config('services.github.client_secret') }}">
                        @error('github_client_secret')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary mr-1 mb-1">
                        <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

