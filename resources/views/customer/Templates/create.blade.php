@extends('layouts/contentLayoutMaster')
@if(isset($template))
    @section('title', __('locale.templates.update_template'))
@else
    @section('title', __('locale.templates.add_template'))
@endif


@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection

@section('content')

    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@if(isset($template)) {{ __('locale.templates.update_template') }} @else {{ __('locale.templates.add_template') }} @endif </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" @if(isset($template)) action="{{ route('customer.templates.update',  $template->uid) }}" @else action="{{ route('customer.templates.store') }}" @endif method="post">
                                @if(isset($template))
                                    {{ method_field('PUT') }}
                                @endif
                                @csrf
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',  isset($template->name) ? $template->name : null) }}" name="name" required placeholder="{{__('locale.labels.required')}}" autofocus>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{__('locale.labels.available_tag')}}</label>
                                                <select class="form-control select2" id="available_tag">
                                                    <option value="phone">{{ __('locale.labels.phone') }}</option>
                                                    <option value="first_name">{{ __('locale.labels.first_name') }}</option>
                                                    <option value="last_name">{{ __('locale.labels.last_name') }}</option>
                                                    <option value="email">{{ __('locale.labels.email') }}</option>
                                                    <option value="username">{{ __('locale.labels.username') }}</option>
                                                    <option value="company">{{ __('locale.labels.company') }}</option>
                                                    <option value="address">{{ __('locale.labels.address') }}</option>
                                                    <option value="birth_date">{{ __('locale.labels.birth_date') }}</option>
                                                    <option value="anniversary_date">{{ __('locale.labels.anniversary_date') }}</option>

                                                    @if($template_tags)
                                                        @foreach($template_tags as $field)
                                                            <option value="{{$field->tag}}">{{ $field->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="text_message" class="required">{{__('locale.labels.message')}}</label>
                                                <textarea class="form-control" name="message" rows="5" id="text_message">{{ old('message',  isset($template->message) ? $template->message : null) }}</textarea>

                                                <small class="text-primary text-uppercase" id="remaining">160 {{ __('locale.labels.characters_remaining') }}</small>
                                                <small class="text-primary text-uppercase pull-right" id="messages">1 {{ __('locale.labels.message') }} (s)</small>
                                                @error('message')
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

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection

@section('page-script')

    <script>
        $(document).ready(function () {
            let $get_msg = $("#text_message"),
                maxCharInitial = 160,
                maxChar = 157,
                messages = 1,
                $remaining = $('#remaining'),
                $messages = $remaining.next(),
                merge_state = $('#available_tag');

            merge_state.on('change', function () {
                const caretPos = $get_msg[0].selectionStart;
                const textAreaTxt = $get_msg.val();
                let txtToAdd = this.value;
                if (txtToAdd) {
                    txtToAdd = '{' + txtToAdd + '}';
                }

                $get_msg.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos));

                get_character();
            });

            function get_character() {
                let totalChar = $get_msg[0].value.length;
                let remainingChar;

                if (totalChar <= maxCharInitial) {
                    remainingChar = maxCharInitial - totalChar;
                    messages = 1;
                } else {
                    totalChar = totalChar - maxCharInitial;
                    messages = Math.ceil(totalChar / maxChar);
                    remainingChar = messages * maxChar - totalChar;
                    messages = messages + 1;
                }

                $remaining.text(remainingChar + " {!! __('locale.labels.characters_remaining')!!}");
                $messages.text(messages + " {!! __('locale.labels.message') !!}" + '(s)');
            }

            $(".select2").select2({
                dropdownAutoWidth: true,
                width: '100%',
                theme: "classic"
            });

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            $get_msg.keyup(get_character);

        });


    </script>
@endsection
