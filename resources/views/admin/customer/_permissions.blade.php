<form class="form form-vertical" action="{{ route('admin.customers.permissions', $customer->uid) }}" method="post">
    @csrf
    <div class="row">
        <div class="col-12">

            @if ($errors->has('permissions.*'))
                <div class="text-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @foreach($permissions as $category)

                <div class="divider divider-left divider-info mt-4">
                    <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.menu.'.$category['title']) }}</div>
                </div>

                <div class="d-flex justify-content-start flex-wrap">
                    @foreach($category['permissions'] as $permission)
                        <div class="vs-checkbox-con vs-checkbox-primary mr-2 mb-1">
                            <input type="checkbox"
                                   @if(isset($existing_permission) && is_array($existing_permission) && in_array($permission['name'], $existing_permission))
                                   checked
                                   @endif
                                   @if($permission['name'] == 'access_backend') disabled @endif
                                   value="{{ $permission['name'] }}"
                                   name="permissions[]"
                            >

                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span>
                            </span>
                            <span class="text-uppercase">{{ __('locale.permission.'.$permission['display_name']) }}</span>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="col-12 d-flex flex-sm-row flex-column justify-content-start mt-1">
            <input type="hidden" value="access_backend" name="permissions[access_backend]">
            <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><i class="feather icon-save"></i>
                {{ __('locale.buttons.save_changes') }}
            </button>
        </div>
    </div>
</form>
