@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Send Using File'))

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts campaign_builder">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" action="{{ route('customer.sms.import_process') }}" method="post">
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
                                                            @foreach (config('app.campaign_db_fields') as $db_key => $db_field)
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

                                <div class="row mt-2">
                                    <div class="col-12">
                                        <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}"/>
                                        <input type="hidden" name="form_data" value="{{ json_encode($form_data, true) }}">
                                        <button type="submit" class="btn btn-primary mb-1">
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
