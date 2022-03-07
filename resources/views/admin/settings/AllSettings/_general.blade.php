<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.general') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-12">
                    <div class="form-group">
                        <label for="app_name" class="required">{{ __('locale.settings.application_name') }}</label>
                        <input type="text" id="app_name" name="app_name" class="form-control" value="{{ config('app.name') }}" maxlength="12" required>
                        <p><small class="text-primary"> {{__('locale.settings.application_name_length')}} </small></p>
                        @error('app_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="app_title" class="required">{{ __('locale.settings.application_title') }}</label>
                        <input type="text" id="app_title" name="app_title" class="form-control" value="{{ config('app.title') }}" required>
                        @error('app_title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="app_keyword">{{ __('locale.settings.application_keyword') }}</label>
                        <input type="text" id="app_keyword" name="app_keyword" class="form-control" value="{{ config('app.keyword') }}">
                        @error('app_keyword')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="company_address" class="required">{{ __('locale.labels.address') }}</label>
                        <textarea id="company_address" name="company_address" rows="6" class="form-control" required>{!! \App\Helpers\Helper::app_config('company_address') !!}</textarea>
                        @error('company_address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="footer_text" class="required">{{ __('locale.settings.footer_text') }}</label>
                        <input type="text" id="footer_text" name="footer_text" class="form-control" value="{!! config('app.footer_text') !!}" required>
                        @error('footer_text')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="app_logo">{{ __('locale.settings.logo') }}</label>
                        <div class="custom-file">
                            <input type="file" name="app_logo" class="custom-file-input" id="app_logo" accept="image/*">
                            <label class="custom-file-label" for="app_logo" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>
                            @error('app_logo')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <p><small class="text-primary"> {{__('locale.settings.logo_size')}} </small></p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="app_favicon">{{ __('locale.settings.favicon') }}</label>
                        <div class="custom-file">
                            <input type="file" name="app_favicon" class="custom-file-input" id="app_favicon" accept="image/*">
                            <label class="custom-file-label" for="app_favicon" data-browse="{{ __('locale.labels.browse') }}">{{__('locale.labels.choose_file')}}</label>
                            @error('app_favicon')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <p><small class="text-primary"> {{__('locale.settings.favicon_size')}} </small></p>
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="country" class="required">{{__('locale.labels.country')}}</label>
                        <select class="form-control select2" id="country" name="country">
                            @foreach(Helper::countries() as $country)
                                <option value="{{$country['name']}}" {{ config('app.country') == $country['name'] ? 'selected': null  }}> {{$country['name']}} </option>
                            @endforeach
                        </select>
                    </div>
                    @error('country')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="timezone" class="required">{{__('locale.labels.timezone')}}</label>
                        <select class="form-control select2" id="timezone" name="timezone">
                            @foreach(\App\Helpers\Helper::timezoneList() as $value => $label)
                                <option value="{{$value}}" @if(config('app.timezone')==$value) selected @endif>{{$label}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('timezone')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="date_format" class="required">{{__('locale.labels.date_format')}}</label>
                        <select class="form-control select2" id="date_format" name="date_format">
                            <option value="d/m/Y" @if(\App\Helpers\Helper::app_config('date_format') == 'd/m/Y') selected @endif>15/05/2016</option>
                            <option value="d.m.Y" @if(\App\Helpers\Helper::app_config('date_format') == 'd.m.Y') selected @endif>15.05.2016</option>
                            <option value="d-m-Y" @if(\App\Helpers\Helper::app_config('date_format') == 'd-m-Y') selected @endif>15-05-2016</option>
                            <option value="m/d/Y" @if(\App\Helpers\Helper::app_config('date_format') == 'm/d/Y') selected @endif>05/15/2016</option>
                            <option value="Y/m/d" @if(\App\Helpers\Helper::app_config('date_format') == 'Y/m/d') selected @endif>2016/05/15</option>
                            <option value="Y-m-d" @if(\App\Helpers\Helper::app_config('date_format') == 'Y-m-d') selected @endif>2016-05-15</option>
                            <option value="M d Y" @if(\App\Helpers\Helper::app_config('date_format') == 'M d Y') selected @endif>May 15 2016</option>
                            <option value="d M Y" @if(\App\Helpers\Helper::app_config('date_format') == 'd M Y') selected @endif>15 May 2016</option>
                            <option value="jS M y" @if(\App\Helpers\Helper::app_config('date_format') == 'jS M y') selected @endif>15th May 16</option>
                        </select>
                    </div>
                    @error('date_format')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="language" class="required">{{__('locale.labels.default')}} {{__('locale.labels.language')}}</label>
                        <select class="form-control select2" id="language" name="language">
                            @foreach($language as $lang)
                                <option value="{{$lang->code}}" @if($lang->code == config('app.locale')) selected @endif>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('language')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="custom_script">{{ __('locale.settings.custom_script') }}</label>
                        <textarea id="custom_script" name="custom_script" class="form-control">{!! config('app.custom_script') !!}</textarea>
                        @error('custom_script')
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
