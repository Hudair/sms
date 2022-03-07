@extends('layouts/contentLayoutMaster')
@if(isset($tag))
    @section('title', __('locale.template_tags.update_template_tag'))
@else
    @section('title', __('locale.template_tags.new_template_tag'))
@endif

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@if(isset($tag)) {{ __('locale.template_tags.update_template_tag') }} @else {{ __('locale.template_tags.new_template_tag') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">

                            <p>{{config('app.name')}} {!!  __('locale.description.template_tag') !!}</p>

                            <form class="form form-vertical" @if(isset($tag)) action="{{ route('admin.tags.update',  $tag->uid) }}" @else action="{{ route('admin.tags.store') }}" @endif method="post">
                                @if(isset($tag))
                                    {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',  isset($tag->name) ? $tag->name : null) }}" name="name" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="type" class="required">{{ __('locale.labels.type') }}</label>
                                                <select class="form-control" name="type" id="type">
                                                    <option value="text" {{ isset($tag->type) && $tag->type == 'text' ? 'selected' : null }}>text</option>
                                                    <option value="email" {{ isset($tag->type) && $tag->type == 'email' ? 'selected' : null }}>email</option>
                                                    <option value="number" {{ isset($tag->type) && $tag->type == 'number' ? 'selected' : null }}>number</option>
                                                    <option value="tel" {{ isset($tag->type) && $tag->type == 'tel' ? 'selected' : null }}>tel</option>
                                                    <option value="url" {{ isset($tag->type) && $tag->type == 'url' ? 'selected' : null }}>url</option>
                                                    <option value="date" {{ isset($tag->type) && $tag->type == 'date' ? 'selected' : null }}>date</option>
                                                </select>
                                                @error('type')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </fieldset>
                                        </div>


                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label for="required" class="required">{{ __('locale.labels.required') }}</label>
                                                <select class="form-control" name="required" id="required">
                                                    <option value="0" {{ isset($tag->required) && $tag->required == 0 ? 'selected' : null }}>{{ __('locale.labels.optional')}} </option>
                                                    <option value="1" {{ isset($tag->required) && $tag->required == 1 ? 'selected' : null }}>{{ __('locale.labels.required') }}</option>
                                                </select>
                                                @error('required')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </fieldset>
                                        </div>


                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{ __('locale.buttons.save') }}</button>
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

@section('page-script')

    <script>
        $(document).ready(function () {

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

        });
    </script>
@endsection
