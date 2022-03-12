@extends('layouts/contentLayoutMaster')

@section('title', __('locale.developers.api_documents'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-knowledge-base.css')) }}">
@endsection

@section('content')
    <!-- Knowledge base question Content  -->
    <section id="api-documentation">
        <div class="row">

            <div class="col-md-12 d-none d-sm-block">
                <p class="row justify-content-center welcome-messages">{{ __('locale.labels.welcome_to_docs', ['brandname' => config('app.name')]) }}</p>
                <p class="row justify-content-center mb-3 welcome-description">
                    {{ __('locale.description.api_docs', ['brandname' => config('app.name')]) }}
                </p>
            </div>

            <div class="col-lg-3 col-md-5 col-12">
                <div class="card">
                    <div class="card-body" id="features">
                        <h5 class="text-success text-uppercase">{{ config('app.name') }} {{ __('locale.labels.api') }}</h5>
                        <a href="#" class="knowledge-base-question">
                            <ul class="list-group list-group-flush mt-1">
                                <li class="list-group-item cursor-pointer contacts-api" id="contacts-api">{{ __('locale.developers.contacts_api') }}</li>
                                <li class="list-group-item cursor-pointer contact-groups-api" id="contact-groups-api">{{ __('locale.developers.contact_groups_api') }}</li>
                                <li class="list-group-item cursor-pointer sms-api" id="sms-api">{{ __('locale.developers.sms_api') }}</li>
                                <li class="list-group-item cursor-pointer voice-api" id="voice-api">{{ __('locale.developers.voice_api') }}</li>
                                <li class="list-group-item cursor-pointer mms-api" id="mms-api">{{ __('locale.developers.mms_api') }}</li>
                                <li class="list-group-item cursor-pointer whatsapp-api" id="whatsapp-api">{{ __('locale.developers.whatsapp_api') }}</li>
                                <li class="list-group-item cursor-pointer profile-api" id="profile-api">{{ __('locale.labels.profile') }} {{ __('locale.labels.api') }}</li>
                            </ul>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-7 col-12">
                <div class="card">
                    <div class="card-body features_description">
                        <div class="title mb-2" id="contacts-api-div">
                            @include('customer.Developers._contacts_api')
                        </div>

                        <div class="title mb-2" id="contact-groups-api-div">
                            @include('customer.Developers._contact_groups_api')
                        </div>

                        <div class="title mb-2" id="sms-api-div">
                            @include('customer.Developers._sms_api')
                        </div>

                        <div class="title mb-2" id="voice-api-div">
                            @include('customer.Developers._voice_api')
                        </div>

                        <div class="title mb-2" id="mms-api-div">
                            @include('customer.Developers._mms_api')
                        </div>

                        <div class="title mb-2" id="whatsapp-api-div">
                            @include('customer.Developers._whatsapp_api')
                        </div>

                        <div class="title mb-2" id="profile-api-div">
                            @include('customer.Developers._profile_api')
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Knowledge base question Content ends -->
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
@endsection


@section('page-script')
    {{-- vendor js files --}}
    <script src="{{ asset(mix('js/scripts/pages/api-documentation.js')) }}"></script>
@endsection
