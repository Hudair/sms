<!-- BEGIN: Footer-->
<footer class="footer {{ $configData['footerType'] }}  {{($configData['footerType']=== 'footer-hidden') ? 'd-none':''}} footer-light">
    <p class="clearfix blue-grey lighten-2 mb-0">
            <span class="float-md-left d-block d-md-inline-block mt-25"> {!! config('app.footer_text') !!}
                <a class="text-bold-800 grey darken-2" href="{{ route('login') }}">{{ config('app.name') }},</a>{{ __('locale.labels.all_rights_reserved') }}</span>
        <button class="btn btn-primary btn-icon scroll-top" type="button">
            <i class="feather icon-arrow-up"></i>
        </button>
    </p>
</footer>
<!-- END: Footer-->
