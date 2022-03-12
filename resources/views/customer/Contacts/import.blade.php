@extends('layouts/contentLayoutMaster')

@section('title', __('locale.contacts.import_contact'))

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body import-file">
                            <form class="form form-vertical"
                                  action="{{ route('customer.contact.import', $contact->uid) }}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <div class='form-check form-switch form-check-primary form-switch-xl'>
                                                <input type="checkbox" class="form-check-input" name="option_toggle" id="option_toggle" checked>
                                                <label class="form-check-label" for="option_toggle">
                                                    <span class="switch-text-left">{{__('locale.labels.import_file')}}</span>
                                                    <span class="switch-text-right">{{__('locale.labels.paste_text')}}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="import_file">

                                            <div class="mb-1 mt-2">
                                                <p class="text-uppercase">{{ __('locale.labels.sample_file') }}</p>
                                                <a href="{{route('sample.file')}}" class="btn btn-primary fw-bold text-uppercase">
                                                    <i data-feather="file-text"></i> {{ __('locale.labels.download_sample_file') }}
                                                </a>

                                            </div>
                                            <div class="mb-1">
                                                <label for="import_file" class="form-label">{{ __('locale.labels.import_file') }}</label>
                                                <div class="us-file-zone us-clickable">
                                                    <input type="file" name="import_file" class="us-file upload-file" id="import_file" accept="text/csv">
                                                    <div class="us-file-message">{{__('locale.filezone.click_here_to_upload')}}
                                                    </div>
                                                    <div class="us-file-footer">
                                                        {!! __('locale.contacts.include_country_code_for_successful_import') !!}
                                                        {!! __('locale.contacts.only_supported_file') !!}
                                                    </div>
                                                </div>
                                                @error('import_file')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </div>


                                            <div class="mb-1">
                                                <div class="form-check me-3 me-lg-5">
                                                    <input type="checkbox" checked value="true" name="header" class="form-check-input">
                                                    <label class="form-check-label">{{ __('locale.filezone.file_contains_header_row') }}?</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="paste_text">

                                            <div class="mb-1">
                                                <label for="recipients" class="form-label">{{ __('locale.labels.paste_text') }}</label>
                                                <span class="text-uppercase float-end">{{ __('locale.labels.total_number_of_recipients') }}:
                                                    <span class="number_of_recipients bold text-success me-5">0</span></span>
                                                <textarea class="form-control" id="recipients" name="recipients" rows="6"></textarea>
                                                <p><small class="text-primary">{!! __('locale.description.paste_text') !!} {!! __('locale.contacts.include_country_code_for_successful_import') !!}</small></p>
                                            </div>

                                            <div class="mb-1">
                                                <div class="btn-group btn-group-sm recipients" role="group">
                                                    <input type="radio" class="btn-check" name="delimiter" value="," id="comma" autocomplete="off" checked/>
                                                    <label class="btn btn-outline-primary" for="comma">, ({{ __('locale.labels.comma') }})</label>

                                                    <input type="radio" class="btn-check" name="delimiter" value=";" id="semicolon" autocomplete="off"/>
                                                    <label class="btn btn-outline-primary" for="semicolon">; ({{ __('locale.labels.semicolon') }})</label>

                                                    <input type="radio" class="btn-check" name="delimiter" value="|" id="bar" autocomplete="off"/>
                                                    <label class="btn btn-outline-primary" for="bar">| ({{ __('locale.labels.bar') }})</label>

                                                    <input type="radio" class="btn-check" name="delimiter" value="tab" id="tab" autocomplete="off"/>
                                                    <label class="btn btn-outline-primary" for="tab">{{ __('locale.labels.tab') }}</label>

                                                    <input type="radio" class="btn-check" name="delimiter" value="new_line" id="new_line" autocomplete="off"/>
                                                    <label class="btn btn-outline-primary" for="new_line">{{ __('locale.labels.new_line') }}</label>

                                                </div>

                                                @error('delimiter')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                                @enderror
                                            </div>

                                        </div>


                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mt-2 mb-1">
                                            <i data-feather="save"></i> {{__('locale.buttons.save')}}
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

        $("body").on("change", ".upload-file", function (e) {
            if ($(this).val() !== '') {
                $('.us-file-message').addClass('us-file-message-done');
            } else {
                $('.us-file-message').removeClass('us-file-message-done');
            }

        });

        let number_of_recipients_ajax = 0,
            number_of_recipients_manual = 0,
            $get_recipients = $('#recipients'),
            firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }

        $('.paste_text').hide();

        $('#option_toggle').on('click', function () {
            if (this.checked) {
                $('.import_file').show();
                $('.paste_text').hide();
            } else {
                $('.import_file').hide();
                $('.paste_text').show();
            }
        });

        function get_delimiter() {
            return $('input[name=delimiter]:checked').val();
        }

        function get_recipients_count() {

            let recipients_value = $get_recipients[0].value.trim();

            if (recipients_value) {
                let delimiter = get_delimiter();

                if (delimiter === ';') {
                    number_of_recipients_manual = recipients_value.split(';').length;
                } else if (delimiter === ',') {
                    number_of_recipients_manual = recipients_value.split(',').length;
                } else if (delimiter === '|') {
                    number_of_recipients_manual = recipients_value.split('|').length;
                } else if (delimiter === 'tab') {
                    number_of_recipients_manual = recipients_value.split(' ').length;
                } else if (delimiter === 'new_line') {
                    number_of_recipients_manual = recipients_value.split('\n').length;
                } else {
                    number_of_recipients_manual = 0;
                }
            } else {
                number_of_recipients_manual = 0;
            }
            let total = number_of_recipients_manual + Number(number_of_recipients_ajax);

            $('.number_of_recipients').text(total);
        }

        $get_recipients.keyup(get_recipients_count);


        $("input[name='delimiter']").change(function () {
            get_recipients_count();
        });

    </script>
@endsection
