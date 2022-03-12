@extends('layouts/contentLayoutMaster')

@section('title', __('locale.contacts.import_contact'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body import-file">
                            <form class="form form-vertical"
                                  action="{{ route('customer.contact.import_process', $contact->uid) }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <thead>
                                            @foreach ($csv_data as $row)
                                                <tr>
                                                    @foreach ($row as $key => $value)
                                                        <td>{{ $value }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                            </thead>
                                            <tbody>
                                            <tr>
                                                @foreach ($csv_data[0] as $key => $value)
                                                    <td>
                                                        <select name="fields[{{ $key }}]" class="form-select select2">
                                                            @foreach (config('app.db_fields') as $db_key => $db_field)
                                                                <option value="{{ $db_key }}">{{ $db_field }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                @endforeach
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}" />
                                        <button type="submit" class="btn btn-primary mt-2 mx-2 mb-1">
                                            <i data-feather="save"></i> {{__('locale.buttons.import')}}
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
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection


@section('page-script')
    <script>

        // Basic Select2 select
        $(".select2").each(function () {
            let $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                width: '100%',
            });
        });


        let firstInvalid = $('form').find('.is-invalid').eq(0);

        if (firstInvalid.length) {
            $('body, html').stop(true, true).animate({
                'scrollTop': firstInvalid.offset().top - 200 + 'px'
            }, 200);
        }
    </script>
@endsection
