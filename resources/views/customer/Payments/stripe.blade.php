@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.pay_payment'))

@section('content')

@endsection

@section('page-script')
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Page js files -->
    <script type="text/javascript">
        // Create an instance of the Stripe object with your publishable API key
        let stripe = Stripe("{{ $publishable_key }}");
        let sessionId = "{{ $session_id }}";

        if (sessionId) {
            stripe.redirectToCheckout({sessionId: sessionId});
        }

    </script>

@endsection
