@extends('layouts.contentLayoutMaster')

@section('title', __('locale.menu.Developers'))


@section('vendor-style')
    {{-- vendor files --}}

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection

@section('content')
    <section id="vertical-tabs">
        <div class="row">
            <div class="col-md-8 col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
                            <span class="btn btn-primary me-1 mb-1 generate-token"><i data-feather="plus-square"></i> {{ __('locale.developers.regenerate_token') }}</span>
                            <a href="#" class="btn btn-success me-1 mb-1" data-bs-toggle="modal" data-bs-target="#sendingServer"><i data-feather="server"></i> {{ __('locale.labels.sending_server') }}</a>

                            <a href="{{ route('customer.developer.docs') }}" class="btn btn-outline-primary mb-1"><i data-feather="book"></i> {{ __('locale.developers.read_the_docs') }}</a>
                            <hr>
                            <div class="mt-2 row">
                                <div class="col-12">
                                    <p class="font-medium-2">{{ __('locale.developers.api_endpoint') }}</p>
                                    <span class="text-primary font-medium-2">{{config('app.url')}}/api/v3/</span>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <p class="font-medium-2">{{ __('locale.developers.api_token') }}</p>
                                    <span class="font-medium-2 text-primary" id="copy-to-clipboard-input">{{ Auth::user()->api_token }} </span>
                                    <span id="btn-copy" data-bs-toggle="tooltip" data-placement="top" title="{{ __('locale.labels.copy') }}"><i data-feather="clipboard" class="font-large-1 text-info cursor-pointer"></i></span>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('customer.Developers._sending_server')
@endsection


@section('vendor-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        let userText = $("#copy-to-clipboard-input");
        let btnCopy = $("#btn-copy");

        // copy text on click
        btnCopy.on("click", function () {

            let clipboardText = "";
            clipboardText = userText.html();
            copyToClipboard(clipboardText);

        })

        // Basic Select2 select
        $(".select2").each(function () {
            let $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $this.parent()
            });
        });

        function copyToClipboard(text) {

            let textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();

            try {
                let successful = document.execCommand('copy');
                let msg = successful ? 'Copied' : 'Failed to copy';

                toastr['success'](msg, '{{__('locale.labels.success')}}!!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                });
            } catch (err) {
                toastr['info']('Oops, unable to copy ' + err, '{{ __('locale.labels.warning') }}!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                    rtl: isRtl
                });
            }
            document.body.removeChild(textArea);
        }


        $(".generate-token").on("click", function (e) {
            e.stopPropagation();
            Swal.fire({
                title: "{{ __('locale.labels.are_you_sure') }}",
                text: "{{ __('locale.labels.able_to_revert') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('locale.labels.generate') }}",
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('customer.developer.generate') }}",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (data) {

                            userText.text(data.token);

                            toastr['success'](data.message, '{{__('locale.labels.success')}}!!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                                rtl: isRtl
                            });
                        },
                        error: function (reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                        closeButton: true,
                                        positionClass: 'toast-top-right',
                                        progressBar: true,
                                        newestOnTop: true,
                                        rtl: isRtl
                                    });
                                });
                            } else {
                                toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                    closeButton: true,
                                    positionClass: 'toast-top-right',
                                    progressBar: true,
                                    newestOnTop: true,
                                    rtl: isRtl
                                });
                            }
                        }
                    })
                }
            })
        });
    </script>
@endsection
