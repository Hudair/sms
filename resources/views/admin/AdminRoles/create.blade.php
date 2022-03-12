@extends('layouts/contentLayoutMaster')

@if(isset($role))
    @section('title', __('locale.role.update_role'))
@else
    @section('title', __('locale.role.create_role'))
@endif


@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-7 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@if(isset($role)) {{ __('locale.role.update_role') }} @else
                                {{ __('locale.role.create_role') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" @if(isset($role)) action="{{ route('admin.roles.update',  $role->uid) }}" @else action="{{ route('admin.roles.store') }}" @endif method="post">
                                @if(isset($role))
                                    {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="row">

                                    <div class="col-12">


                                        <div class="form-group">
                                            <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',  $role->name ?? null) }}" name="name" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                            @error('name')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>


                                        <div class="mt-4"></div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" />
                                            <label class="form-check-label text-uppercase" for="selectAll">{{ __('locale.labels.select_all') }}</label>
                                        </div>
                                        @foreach($permissions as $category)

                                            <div class="divider divider-start divider-info mt-4">
                                                <div class="divider-text text-uppercase fw-bold text-primary">{{ __('locale.menu.'.$category['title']) }}</div>
                                            </div>

                                            <div class="d-flex justify-content-start flex-wrap">
                                                @foreach($category['permissions'] as $permission)
                                                    <div class="form-check me-3 me-lg-5 mt-1">
                                                        <input type="checkbox"
                                                               @if(isset($role))
                                                               @if(isset($existing_permission) && is_array($existing_permission) && in_array($permission['name'], $existing_permission))
                                                               checked
                                                               @endif
                                                               @else
                                                               checked
                                                               @endif
                                                               value="{{ $permission['name'] }}"
                                                               name="permissions[]"
                                                               id="{{ $permission['name'] }}"
                                                               class="form-check-input"
                                                        >
                                                        <label class="form-check-label text-uppercase" for="{{ $permission['name'] }}"> {{ __('locale.permission.'.$permission['display_name']) }} </label>

                                                    </div>

                                                @endforeach
                                            </div>
                                        @endforeach

                                    </div>


                                    <div class="col-12 mt-2">
                                        <input type="hidden" value="access backend" name="permissions[]">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1">
                                            <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                                        </button>
                                    </div>


                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection

@section('page-script')
    <script>
        // Select All checkbox click
        const selectAll = document.querySelector('#selectAll'),
            checkboxList = document.querySelectorAll('[type="checkbox"]');
        selectAll.addEventListener('change', t => {
            checkboxList.forEach(e => {
                e.checked = t.target.checked;
            });
        });
    </script>
@endsection
