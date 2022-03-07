<form class="form form-vertical" action="{{ route('user.account.update') }}" method="post">
    @method('PATCH')
    @csrf
    <div class="row">

        <div class="col-12 col-sm-6">

            <div class="form-group">
                <label for="email" class="required">{{__('locale.labels.email')}}</label>
                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ $user->email }}" name="email" required>
                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="first_name" class="required">{{__('locale.labels.first_name')}}</label>
                        <input type="text" id="first_name"
                               class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ $user->first_name }}" name="first_name" required>
                        @error('first_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-sm-6">

                    <div class="form-group">
                        <label for="last_name">{{__('locale.labels.last_name')}}</label>
                        <input type="text" id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ $user->last_name }}" name="last_name">
                        @error('last_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 col-sm-6">
            <div class="form-group">
                <label for="timezone" class="required">{{__('locale.labels.timezone')}}</label>
                <select class="form-control select2" id="timezone" name="timezone">
                    @foreach(\App\Library\Tool::allTimeZones() as $timezone)
                        <option value="{{$timezone['zone']}}" {{ $user->timezone == $timezone['zone'] ? 'selected': null }}> {{ $timezone['text'] }}</option>
                    @endforeach
                </select>
                @error('timezone')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="locale" class="required">{{__('locale.labels.language')}}</label>
                <select class="form-control select2" id="locale" name="locale">
                    @foreach($languages as $language)
                        <option value="{{ $language->code }}" {{ $user->locale == $language->code ? 'selected': null }}> {{ $language->name }}</option>
                    @endforeach
                </select>
                @error('locale')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>

        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
            <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i class="feather icon-save"></i> {{__('locale.buttons.save_changes')}}</button>
        </div>

    </div>
</form>
