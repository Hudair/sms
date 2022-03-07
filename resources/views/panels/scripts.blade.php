{{-- Vendor Scripts --}}
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@yield('vendor-script')
{{-- Theme Scripts --}}
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/components.js')) }}"></script>

<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

@if($configData['blankPage'] == false)
    <script src="{{ asset(mix('js/scripts/footer.js')) }}"></script>
@endif

@if(Auth::check() && Auth::user()->active_portal == 'customer' && Auth::user()->is_customer == 1)
    @if(Auth::user()->customer->activeSubscription() == null)
        <script>
            toastr.warning("{!! __('locale.customer.no_active_subscription') !!}", 'Warning!', {
                "timeOut": 0,
                positionClass: 'toast-bottom-right',
                containerId: 'toast-bottom-right',
                closeButton: true,
            });
        </script>
    @endif
@endif

{{-- page script --}}
@yield('page-script')
@if(session()->has('message'))
    <script>
        let type = "{{ Session::get('status', 'success') }}";
        switch (type) {
            case 'info':
                toastr.info("{{ Session::get('message') }}", 'Information', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
                break;

            case 'warning':
                toastr.warning("{{ Session::get('message') }}", 'Warning!', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
                break;

            case 'success':
                toastr.success("{{ Session::get('message') }}", 'Success!!', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
                break;

            case 'error':
                toastr.error("{{ Session::get('message') }}", 'Oops..!!', {
                    positionClass: 'toast-top-right',
                    containerId: 'toast-top-right',
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
                break;
        }
    </script>
@endif
