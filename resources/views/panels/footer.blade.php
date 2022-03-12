<!-- BEGIN: Footer-->
<footer class="footer footer-light {{($configData['footerType'] === 'footer-hidden') ? 'd-none':''}} {{$configData['footerType']}}">
    <p class="clearfix mb-0">
        <span class="float-md-start d-block d-md-inline-block mt-25"> {!! config('app.footer_text') !!}
            <a class="ms-25" href="{{ route('login') }}">{{ config('app.name') }},</a>
            <span class="d-none d-sm-inline-block">{{ __('locale.labels.all_rights_reserved') }}</span>
        </span>
        <button class="btn btn-primary btn-icon scroll-top" type="button">
            <i data-feather="arrow-up"></i>
        </button>
    </p>
</footer>
<!-- END: Footer-->
