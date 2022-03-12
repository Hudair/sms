<div class="card">
    <div class="card-body pt-1">
        <form class="form form-vertical mt-2 pt-50" action="{{ route('admin.customers.permissions', $customer->uid) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-12">

                    @if ($errors->has('permissions.*'))
                        <p><small class="text-danger">{{ $errors->first() }}</small></p>
                    @endif

                    @foreach($permissions as $category)

                        <div class="divider divider-start divider-info mt-4">
                            <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.menu.'.$category['title']) }}</div>
                        </div>

                        <div class="d-flex justify-content-start flex-wrap">
                            @foreach($category['permissions'] as $permission)
                                <div class="form-check me-3 me-lg-5 mt-1">
                                    <input type="checkbox"
                                           @if(isset($existing_permission) && is_array($existing_permission) && in_array($permission['name'], $existing_permission))
                                           checked
                                           @endif
                                           @if($permission['name'] == 'access_backend') disabled @endif
                                           value="{{ $permission['name'] }}"
                                           name="permissions[]"
                                           class="form-check-input"
                                           id="{{ $permission['name'] }}"
                                    >
                                    <label class="form-check-label text-uppercase" for="{{ $permission['name'] }}"> {{ __('locale.permission.'.$permission['display_name']) }} </label>


                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="col-12 d-flex flex-sm-row flex-column justify-content-start mt-1">
                    <input type="hidden" value="access_backend" name="permissions[access_backend]">
                    <button type="submit" class="btn btn-primary mt-1 me-1"><i data-feather="save"></i>
                        {{ __('locale.buttons.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
