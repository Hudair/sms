@extends('layouts/contentLayoutMaster')

@section('title', __('locale.contacts.update_contact'))
@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('locale.contacts.update_contact') }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('customer.contact.update', ['contact' => $contact->uid , 'contact_id' => $data->uid]) }}" method="post">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="phone" class="required">{{__('locale.labels.phone')}}</label>
                                            <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $data->phone }}" name="phone" required>
                                            @error('phone')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="first_name">{{__('locale.labels.first_name')}}</label>
                                            <input
                                                    type="text"
                                                    id="first_name"
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    value="{{ old('first_name',  isset($data->first_name) ? $data->first_name : null) }}"
                                                    name="first_name"
                                            >
                                            @error('first_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="last_name">{{__('locale.labels.last_name')}}</label>
                                            <input
                                                    type="text"
                                                    id="last_name"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    value="{{ old('last_name',  isset($data->last_name) ? $data->last_name : null) }}"
                                                    name="last_name"
                                            >
                                            @error('last_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email">{{__('locale.labels.email')}}</label>
                                            <input
                                                    type="email"
                                                    id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email',  isset($data->email) ? $data->email : null) }}"
                                                    name="email"
                                            >
                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="username">{{__('locale.labels.username')}}</label>
                                            <input
                                                    type="text"
                                                    id="username"
                                                    class="form-control @error('username') is-invalid @enderror"
                                                    value="{{ old('username',  isset($data->username) ? $data->username : null) }}"
                                                    name="username"
                                            >
                                            @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="company">{{__('locale.labels.company')}}</label>
                                            <input
                                                    type="text"
                                                    id="company"
                                                    class="form-control @error('company') is-invalid @enderror"
                                                    value="{{ old('company',  isset($data->company) ? $data->company : null) }}"
                                                    name="company"
                                            >
                                            @error('company')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="address">{{__('locale.labels.address')}}</label>
                                            <input
                                                    type="text"
                                                    id="address"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    value="{{ old('address',  isset($data->address) ? $data->address : null) }}"
                                                    name="address"
                                            >
                                            @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="birth_date">{{__('locale.labels.birth_date')}}</label>
                                            <input type="text"
                                                   id="birth_date"
                                                   class="form-control pickadate @error('birth_date') is-invalid @enderror"
                                                   value="{{ old('birth_date',  isset($data->birth_date) ? $data->birth_date->format('Y-m-d') : null) }}"
                                                   name="birth_date"
                                            >
                                            @error('birth_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="anniversary_date">{{__('locale.labels.anniversary_date')}}</label>
                                            <input
                                                    type="text"
                                                    id="anniversary_date"
                                                    class="form-control pickadate @error('anniversary_date') is-invalid @enderror"
                                                    value="{{ old('anniversary_date',  isset($data->anniversary_date) ? $data->anniversary_date->format('Y-m-d') : null) }}"
                                                    name="anniversary_date"
                                            >
                                            @error('anniversary_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        @if($custom_fields)
                                            @foreach($custom_fields as $key => $field)
                                                <div class="form-group">
                                                    <label for="{{$field->tag}}" @if($field->required == 1) class="required" @endif>{{ $field->name }}</label>
                                                    <input
                                                            type="{{ $field->type }}"
                                                            id="{{$field->tag}}"
                                                            class="form-control @if($field->type == 'date') pickadate @endif"
                                                            value="{{ old($field->tag,  isset($field->value) ? $field->value : null) }}"
                                                            name="custom[{{$key}}][value]"
                                                            @if($field->required == 1) required @endif
                                                    >
                                                    <input type="hidden" name="custom[{{$key}}][name]" value="{{$field->name}}">
                                                    <input type="hidden" name="custom[{{$key}}][type]" value="{{$field->type}}">
                                                    <input type="hidden" name="custom[{{$key}}][tag]" value="{{$field->tag}}">
                                                    <input type="hidden" name="custom[{{$key}}][required]" value="{{$field->required}}">
                                                    @error($field->tag)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            @endforeach
                                        @else
                                            @if($template_tags)
                                                @foreach($template_tags as $key => $field)
                                                    <div class="form-group">
                                                        <label for="{{$field->tag}}" @if($field->required == 1) class="required" @endif>{{ $field->name }}</label>
                                                        <input
                                                                type="{{ $field->type }}"
                                                                id="{{$field->tag}}"
                                                                class="form-control @if($field->type == 'date') pickadate @endif"
                                                                value="{{ old($field->tag)}}"
                                                                name="custom[{{$key}}][value]"
                                                                @if($field->required == 1) required @endif
                                                        >
                                                        <input type="hidden" name="custom[{{$key}}][name]" value="{{$field->name}}">
                                                        <input type="hidden" name="custom[{{$key}}][type]" value="{{$field->type}}">
                                                        <input type="hidden" name="custom[{{$key}}][tag]" value="{{$field->tag}}">
                                                        <input type="hidden" name="custom[{{$key}}][required]" value="{{$field->required}}">
                                                        @error($field->tag)
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1">
                                            <i class="feather icon-save"></i> {{__('locale.buttons.update')}}
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
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

        $('.pickadate').pickadate({
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true
        });

    </script>
@endsection
