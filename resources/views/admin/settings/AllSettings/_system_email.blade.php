<div class="col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.email') }}" method="post">
            @csrf
            <div class="row">

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="driver" class="required">{{ __('locale.settings.method_for_sending') }}</label>
                        <select class="form-control gateway" name="driver" id="driver">
                            <option value="sendmail" @if(config('mail.driver') =='sendmail') selected @endif>Sendmail</option>
                            <option value="smtp" @if(config('mail.driver') =='smtp') selected @endif> SMTP</option>
                        </select>
                        @error('driver')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6"></div>


                <div class="col-md-4 col-12 show-smtp">
                    <div class="form-group">
                        <label for="host" class="required"> {{ __('locale.labels.host_name') }}</label>
                        <input type="text" class="form-control" name="host" value="{{config('mail.host')}}">

                        @error('host')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>


                <div class="col-md-4 col-12 show-smtp">
                    <div class="form-group">
                        <label for="port" class="required"> {{ __('locale.labels.port') }}</label>
                        <input type="text" class="form-control" name="port" value="{{config('mail.port')}}">

                        @error('port')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <div class="col-md-4 col-12 show-smtp">
                    <div class="form-group">
                        <label for="encryption" class="required">{{__('locale.labels.encryption')}}</label>
                        <select name="encryption" class="form-control" id="encryption">
                            <option value="tls" @if(config('mail.encryption')=='tls')  selected @endif>TLS</option>
                            <option value="ssl" @if(config('mail.encryption')=='ssl')selected @endif>SSL</option>
                        </select>

                        @error('port')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6 col-12 show-smtp">
                    <div class="form-group">
                        <label for="username" class="required"> {{ __('locale.labels.username') }}</label>
                        <input type="text" class="form-control" name="username" value="{{config('mail.username')}}">

                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>


                <div class="col-md-6 col-12 show-smtp">
                    <div class="form-group">
                        <label for="password" class="required"> {{ __('locale.labels.password') }}</label>
                        <input type="text" class="form-control" name="password" value="{{config('mail.password')}}">

                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="from_email" class="required">{{ __('locale.settings.from_email') }}</label>
                        <input type="email" id="from_email" name="from_email" class="form-control" value="{{config('mail.from.address')}}" required>
                        @error('from_email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label for="from_name" class="required">{{ __('locale.settings.from_name') }}</label>
                        <input type="text" id="from_name" name="from_name" class="form-control" value="{{ config('mail.from.name') }}" required>
                        @error('from_name')
                        <div class="invalid-feedback">
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
