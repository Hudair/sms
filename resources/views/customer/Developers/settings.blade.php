@extends('layouts.contentLayoutMaster')

@section('title', __('locale.menu.Developers'))


@section('vendor-style')
    {{-- vendor files --}}

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection

@section('content')
    <section id="vertical-tabs">
        <div class="row match-height">
            <div class="col-6">
                <div class="card overflow-hidden  pb-2">
                    <div class="card-header"></div>
                    <div class="card-content">
                        <div class="card-body">
                            <span class="btn btn-primary mr-1 mb-1 generate-token"><i class="feather icon-plus"></i> {{ __('locale.developers.regenerate_token') }}</span>
                            <a href="{{ route('customer.developer.docs') }}" class="btn btn-outline-primary mb-1"><i class="feather icon-book"></i> {{ __('locale.developers.read_the_docs') }}</a>
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
                                    <i class="feather icon-clipboard font-large-1 text-info" data-toggle="tooltip" data-placement="top"
                                       title="{{ __('locale.labels.copy') }}" id="btn-copy"></i>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('vendor-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
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

        function copyToClipboard(text) {

            let textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();

            try {
                let successful = document.execCommand('copy');
                let msg = successful ? 'Copied' : 'Failed to copy';

                toastr.success(msg, 'Success!!', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
            } catch (err) {
                toastr.info('Oops, unable to copy ' + err, 'Success!!', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
            }
            document.body.removeChild(textArea);
        }


        $(".generate-token").on("click", function (e) {
            e.stopPropagation();
            Swal.fire({
                title: "{{ __('locale.labels.are_you_sure') }}",
                text: "{{ __('locale.labels.able_to_revert') }}",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('locale.labels.generate') }}",
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
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

                            toastr.success(data.message, "Success!!", {
                                positionClass: 'toast-top-right',
                                containerId: 'toast-top-right',
                                progressBar: true,
                                closeButton: true,
                                newestOnTop: true
                            });
                        },
                        error: function (reject) {
                            if (reject.status === 422) {
                                let errors = reject.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    toastr.warning(value[0], "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                });
                            } else {
                                toastr.warning(reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                    positionClass: 'toast-top-right',
                                    containerId: 'toast-top-right',
                                    progressBar: true,
                                    closeButton: true,
                                    newestOnTop: true
                                });
                            }
                        }
                    })
                }
            })
        });
    </script>
@endsection
