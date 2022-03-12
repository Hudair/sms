<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.general') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-12">
                    <div class="mb-1">
                        <label for="app_name" class="form-label required">{{ __('locale.settings.application_name') }}</label>
                        <input type="text" id="app_name" name="app_name" class="form-control" value="{{ config('app.name') }}" required>
                        @error('app_name')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="app_title" class="form-label required">{{ __('locale.settings.application_title') }}</label>
                        <input type="text" id="app_title" name="app_title" class="form-control" value="{{ config('app.title') }}" required>
                        @error('app_title')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="app_keyword" class="form-label">{{ __('locale.settings.application_keyword') }}</label>
                        <input type="text" id="app_keyword" name="app_keyword" class="form-control" value="{{ config('app.keyword') }}">
                        @error('app_keyword')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="company_address" class="form-label required">{{ __('locale.labels.address') }}</label>
                        <textarea id="company_address" name="company_address" rows="6" class="form-control" required>{!! \App\Helpers\Helper::app_config('company_address') !!}</textarea>
                        @error('company_address')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="footer_text" class="form-label required">{{ __('locale.settings.footer_text') }}</label>
                        <input type="text" id="footer_text" name="footer_text" class="form-control" value="{!! config('app.footer_text') !!}" required>
                        @error('footer_text')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="app_logo" class="form-label">{{ __('locale.settings.logo') }}</label>
                        <input type="file" name="app_logo" class="form-control" id="app_logo" accept="image/*"/>
                        <p><small class="text-primary"> {{__('locale.settings.logo_size')}} </small></p>

                        @error('app_logo')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="app_favicon" class="form-label">{{ __('locale.settings.favicon') }}</label>
                        <input type="file" name="app_favicon" class="form-control" id="app_favicon" accept="image/*"/>
                        <p><small class="text-primary"> {{__('locale.settings.favicon_size')}} </small></p>
                        @error('app_favicon')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="country" class="form-label required">{{__('locale.labels.country')}}</label>
                        <select class="form-select select2" id="country" name="country">
                            @foreach(Helper::countries() as $country)
                                <option value="{{$country['name']}}" {{ config('app.country') == $country['name'] ? 'selected': null  }}> {{$country['name']}} </option>
                            @endforeach
                        </select>
                    </div>
                    @error('country')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="timezone" class="form-label required">{{__('locale.labels.timezone')}}</label>
                        <select class="form-select select2" id="timezone" name="timezone">
                            @foreach(\App\Helpers\Helper::timezoneList() as $value => $label)
                                <option value="{{$value}}" @if(config('app.timezone')==$value) selected @endif>{{$label}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('timezone')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="date_format" class="form-label required">{{__('locale.labels.date_format')}}</label>
                        <select class="form-select select2" id="date_format" name="date_format">
                            <option value="d/m/Y" @if(config('app.date_format') == 'd/m/Y') selected @endif>15/05/2016</option>
                            <option value="d.m.Y" @if(config('app.date_format') == 'd.m.Y') selected @endif>15.05.2016</option>
                            <option value="d-m-Y" @if(config('app.date_format') == 'd-m-Y') selected @endif>15-05-2016</option>
                            <option value="m/d/Y" @if(config('app.date_format') == 'm/d/Y') selected @endif>05/15/2016</option>
                            <option value="Y/m/d" @if(config('app.date_format') == 'Y/m/d') selected @endif>2016/05/15</option>
                            <option value="Y-m-d" @if(config('app.date_format') == 'Y-m-d') selected @endif>2016-05-15</option>
                            <option value="M d Y" @if(config('app.date_format') == 'M d Y') selected @endif>May 15 2016</option>
                            <option value="d M Y" @if(config('app.date_format') == 'd M Y') selected @endif>15 May 2016</option>
                            <option value="jS M y" @if(config('app.date_format') == 'jS M y') selected @endif>15th May 16</option>
                        </select>
                    </div>
                    @error('date_format')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>


                <div class="col-12">
                    <div class="mb-1">
                        <label for="language" class="form-label required">{{__('locale.labels.default')}} {{__('locale.labels.language')}}</label>
                        <select class="form-select select2" id="language" name="language">
                            @foreach($language as $lang)
                                <option value="{{$lang->code}}" @if($lang->code == config('app.locale')) selected @endif>{{$lang->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('language')
                    <p><small class="text-danger">{{ $message }}</small></p>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="mb-1">
                        <label for="custom_script">{{ __('locale.settings.custom_script') }}</label>
                        <textarea id="custom_script" name="custom_script" class="form-control">{!! config('app.custom_script') !!}</textarea>
                        @error('custom_script')
                        <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>


                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary mb-1">
                        <i data-feather="save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
