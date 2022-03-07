<form class="form form-vertical" action="{{ route('user.account.change.password') }}" method="post">
    @csrf
    <div class="row">

        <div class="col-12 col-sm-6">

            <div class="form-group">
                <label for="current_password" class="required">{{__('locale.labels.current_password')}}</label>
                <div class="position-relative show_hide_password">
                    <input type="password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                           value="{{ old('current_password') }}" name="current_password">

                    <div class="form-control-position cursor-pointer">
                        <i class="feather icon-eye-off"></i>
                    </div>

                    @if($errors->has('current_password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('current_password') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="required">{{__('locale.labels.new_password')}}</label>
                <div class="position-relative show_hide_password">
                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password">

                    <div class="form-control-position cursor-pointer">
                        <i class="feather icon-eye-off"></i>
                    </div>

                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="required">{{__('locale.labels.new_password_confirmation')}}</label>
                <div class="position-relative show_hide_password">
                    <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                           value="{{ old('password_confirmation') }}" name="password_confirmation">

                    <div class="form-control-position cursor-pointer">
                        <i class="feather icon-eye-off"></i>
                    </div>

                    @if($errors->has('password_confirmation'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password_confirmation') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>


        <div class="col-12 d-flex flex-sm-row flex-column mt-1">
            <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i class="feather icon-save"></i> {{__('locale.buttons.save_changes')}}</button>
        </div>

    </div>
</form>
