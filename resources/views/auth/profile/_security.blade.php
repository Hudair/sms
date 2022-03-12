<div class="card">
    <div class="card-body py-2 my-25">
        <form class="form form-vertical" action="{{ route('user.account.change.password') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-12 col-sm-6">

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label required" for="password">{{ __('locale.labels.current_password') }}</label>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" id="current_password" class="form-control @error('current_password') is-invalid @enderror"
                                       value="{{ old('current_password') }}" name="current_password">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>

                            @error('current_password')
                            <p><small class="text-danger">{{ $errors->first('current_password') }}</small></p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label required" for="password">{{ __('locale.labels.new_password') }}</label>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" name="password">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>

                            @error('password')
                            <p><small class="text-danger">{{ $errors->first('password') }}</small></p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label required" for="password">{{ __('locale.labels.new_password_confirmation') }}</label>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
                                       value="{{ old('password_confirmation') }}" name="password_confirmation">
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>

                            @error('password_confirmation')
                            <p><small class="text-danger">{{ $errors->first('password_confirmation') }}</small></p>
                            @enderror
                        </div>
                    </div>

                </div>


                <div class="col-12 d-flex flex-sm-row flex-column mt-1">
                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0"><i data-feather="save"></i> {{__('locale.buttons.save_changes')}}</button>
                </div>

            </div>
        </form>
    </div>
</div>
