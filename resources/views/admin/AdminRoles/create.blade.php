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
            <div class="col-md-6 col-12">

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
                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',  isset($role->name) ? $role->name : null) }}" name="name" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="mt-4"></div>

                                        @foreach($permissions as $category)

                                            <div class="divider divider-left divider-info mt-4">
                                                <div class="divider-text text-uppercase text-bold-600 text-primary">{{ __('locale.menu.'.$category['title']) }}</div>
                                            </div>

                                            <div class="d-flex justify-content-start flex-wrap">
                                                @foreach($category['permissions'] as $permission)
                                                    <div class="vs-checkbox-con vs-checkbox-primary mr-2 mb-1">
                                                        <input type="checkbox"
                                                               @if(isset($role))
                                                               @if(isset($existing_permission) && is_array($existing_permission) && in_array($permission['name'], $existing_permission))
                                                               checked
                                                               @endif
                                                               @else
                                                               checked
                                                               @endif
                                                               @if($permission['name'] == 'access backend') disabled @endif
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
