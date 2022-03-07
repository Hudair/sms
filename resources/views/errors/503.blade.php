@extends('layouts/fullLayoutMaster')

@section('title', __('locale.http.503.title'))
@section('code', '503')

@section('content')
    <!-- maintenance -->
    <section class="row flexbox-container">
        <div class="col-12 d-flex justify-content-center">
            <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                <div class="card-content">
                    <div class="card-body text-center">
                        <img src="{{ asset('images/pages/maintenance-2.png') }}" class="img-fluid align-self-center" alt="{{ config('app.name') }}">
                        <h1 class="font-large-2 my-1">{{__('locale.http.503.title')}}!</h1>
                        <p class="px-2">
                            {{ __($exception->getMessage() ?: __('locale.http.503.description')) }}
                        </p>

                        @if(!empty(Helper::app_config('maintenance_mode_end')))
                            <p>We will be back in</p>
                            <h2 class="text-primary " id="demo"></h2>
                            {{--                       $date = date('M j, Y G:H:s', $time);--}}
                            <input type="hidden" value="{{ Helper::app_config('maintenance_mode_end') }}" id="getDate">

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- maintenance end -->
@endsection


@if(!empty(Helper::app_config('maintenance_mode_end')))

    @section('page-script')
        <script>

            let getDate = $('#getDate').val();

            // Set the date we're counting down to
            let countDownDate = new Date(getDate).getTime();

            // Update the count down every 1 second
            let x = setInterval(function () {

                // Get today's date and time
                let now = new Date().getTime();

                // Find the distance between now and the count down date
                let distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="demo"
                document.getElementById("demo").innerHTML = days + "d " + hours + "h "
                    + minutes + "m " + seconds + "s ";

                // If the count down is finished, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("demo").innerHTML = "EXPIRED";
                }
            }, 1000);
        </script>
    @endsection

@endif
