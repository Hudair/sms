
@extends('layouts/contentLayoutMaster')
@if(isset($currency))
    @section('title', __('locale.currencies.update_currency'))
@else
    @section('title', __('locale.currencies.add_new_currency'))
@endif

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@if(isset($currency)) {{ __('locale.currencies.update_currency') }} @else
                            {{ __('locale.currencies.add_new_currency') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" @if(isset($currency)) action="{{ route('admin.currencies.update',  $currency->uid) }}" @else action="{{ route('admin.currencies.store') }}" @endif method="post">
                                @if(isset($currency))
                                {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',  isset($currency->name) ? $currency->name : null) }}" name="name" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="code" class="required">{{ __('locale.currencies.code') }}</label>
                                                <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code',  isset($currency->code) ? $currency->code : null) }}"  name="code" required placeholder="{{__('locale.labels.required')}}">
                                                @error('code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="format" class="required">{{ __('locale.currencies.format') }}</label>
                                                <input type="text" id="format" class="form-control @error('format') is-invalid @enderror" value="{{ old('format',  isset($currency->format) ? $currency->format : null) }}"  name="format" required placeholder="${PRICE} or {PRICE}$">
                                                @error('format')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
                                            <button type="reset" class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-refresh-cw"></i> {{ __('locale.buttons.reset') }}</button>
                                        </div>

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
