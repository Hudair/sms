@extends('layouts/contentLayoutMaster')

@section('title', $server['name'])


@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <form class="form form-vertical" @if(isset($server['id'])) action="{{ route('admin.sending-servers.update',  $server['uid']) }}" @else action="{{ route('admin.sending-servers.store') }}" @endif method="post">
                    @if(isset($server['id']))
                        {{ method_field('PUT') }}
                    @endif
                    @csrf

                    {{--Update Server Credential--}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"> {{__('locale.sending_servers.update_credentials')}} </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                @switch($server['settings'])
                                    @case('Twilio')
                                    @case('TwilioCopilot')
                                    <p>{!!  __('locale.description.twilio', ['brandname' => config('app.name'), 'url' => route('inbound.twilio')]) !!}</p>
                                    @break

                                    @case('ClickatellTouch')
                                    @case('ClickatellCentral')
                                    <p>{!!  __('locale.description.clickatell') !!} {{config('app.name')}}</p>
                                    @break

                                    @case('RouteMobile')
                                    <p> {!! __('locale.description.route_mobile', ['brandname' => config('app.name'), 'url' => route('dlr.routemobile')]) !!}.</p>
                                    @break

                                    @case('TextLocal')
                                    <p> {!! __('locale.description.text_local', ['brandname' => config('app.name'), 'url' => route('inbound.textlocal')]) !!}</p>
                                    @break

                                    @case('msg91')
                                    <p> {!! __('locale.description.mgs91') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('Plivo')
                                    @case('PlivoPowerpack')
                                    <p> {!! __('locale.description.plivo', ['brandname' => config('app.name'), 'url' => route('inbound.plivo')]) !!}</p>
                                    @break

                                    @case('SMSGlobal')
                                    <p> {!! __('locale.description.sms_global', ['brandname' => config('app.name'), 'url' => route('dlr.smsglobal')]) !!}</p>
                                    @break

                                    @case('BulkSMS')
                                    <p> {!! __('locale.description.bulk_sms', ['brandname' => config('app.name'), 'url' => route('inbound.bulksms')]) !!}</p>
                                    @break

                                    @case('Vonage')
                                    <p> {!! __('locale.description.vonage', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.vonage'), 'dlr_url' => route('dlr.vonage')]) !!}</p>
                                    @break

                                    @case('Infobip')
                                    <p> {!! __('locale.description.infobip') !!}</p>
                                    @break

                                    @case('1s2u')
                                    <p> {!! __('locale.description.1s2u') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('SmsGatewayMe')
                                    <p> {!! __('locale.description.sms_gateway_me') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('MessageBird')
                                    <p> {!! __('locale.description.messagebird', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.messagebird')]) !!}</p>
                                    @break

                                    @case('AmazonSNS')
                                    <p> {!! __('locale.description.amazon_sns') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('Tyntec')
                                    <p> {!! __('locale.description.tyntec') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('WhatsAppChatApi')
                                    <p> {!! __('locale.description.whatsapp_chat_api') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('KarixIO')
                                    <p> {!! __('locale.description.karixio') !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('SignalWire')
                                    <p> {!! __('locale.description.signal_wire', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.signalwire')]) !!}</p>
                                    @break

                                    @case('FlowRoute')
                                    <p> {!! __('locale.description.flowroute', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.flowroute')]) !!}</p>
                                    @break

                                    @case('Telnyx')
                                    <p> {!! __('locale.description.telnyx', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.telnyx')]) !!} </p>
                                    @break

                                    @case('Bandwidth')
                                    <p> {!! __('locale.description.bandwidth', ['brandname' => config('app.name')]) !!} </p>
                                    @break

                                    @case('SMPP')
                                    <p> {!! __('locale.description.smpp', ['brandname' => config('app.name')]) !!} {{config('app.name')}}.</p>
                                    @break

                                    @case('Teletopiasms')
                                    <p> {!! __('locale.description.teletopiasms', ['brandname' => config('app.name'), 'url' => route('inbound.teletopiasms')]) !!}</p>
                                    @break

                                    @case('EasySendSMS')
                                    <p> {!! __('locale.description.easysendsms', ['brandname' => config('app.name'), 'inbound_url' => route('inbound.easysendsms'), 'dlr_url' => route('dlr.easysendsms')]) !!}</p>
                                    @break

                                @endswitch

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="name">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $server['name'] }}" name="name" required>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if( $server['settings'] != 'Twilio' && $server['settings'] != 'Zang' && $server['settings'] != 'Plivo' && $server['settings'] != 'PlivoPowerpack' && $server['settings'] != 'AmazonSNS' && $server['settings'] != 'TeleSign' && $server['settings'] != 'TwilioCopilot')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    @if($server['settings'] == 'SignalWire')
                                                        <label class="required" for="api_link">SPACE URL</label>
                                                    @elseif($server['settings'] == 'SMPP')
                                                        <label class="required" for="api_link">IP/DOMAIN</label>
                                                    @else
                                                        <label class="required" for="api_link">API Link</label>
                                                    @endif
                                                    <input type="text" id="api_link" class="form-control @error('api_link') is-invalid @enderror" value="{{ $server['api_link'] }}" name="api_link" required>
                                                    @error('api_link')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        @if($server['settings'] == 'Twilio' || $server['settings'] == 'TwilioCopilot' )
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="account_sid">Account Sid</label>
                                                    <input type="text" id="account_sid" class="form-control @error('account_sid') is-invalid @enderror" value="{{ $server['account_sid'] }}" name="account_sid" required>
                                                    @error('account_sid')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        @if($server['settings'] == 'Plivo' || $server['settings'] == 'PlivoPowerpack'  || $server['settings'] == 'KarixIO' )
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="auth_id">Auth ID</label>
                                                    <input type="text" id="auth_id" class="form-control @error('auth_id') is-invalid @enderror" value="{{ $server['auth_id'] }}" name="auth_id" required>
                                                    @error('auth_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        @if($server['settings'] == 'SpeedaMobile' || $server['settings'] == 'SMSala')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="auth_id">API ID</label>
                                                    <input type="text" id="auth_id" class="form-control @error('auth_id') is-invalid @enderror" value="{{ $server['auth_id'] }}" name="auth_id" required>
                                                    @error('auth_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="password">API Password</label>
                                                    <input type="text" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ $server['password'] }}" name="password" required>
                                                    @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        @if($server['settings'] == 'EnableX')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="application_id">APP ID</label>
                                                    <input type="text" id="application_id" class="form-control @error('application_id') is-invalid @enderror" value="{{ $server['application_id'] }}" name="application_id" required>
                                                    @error('application_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_key">APP Key</label>
                                                    <input type="text" id="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ $server['api_key'] }}" name="api_key" required>
                                                    @error('api_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c1">Campaign ID</label>
                                                    <input type="text" id="c1" class="form-control @error('c1') is-invalid @enderror" value="{{ $server['c1'] }}" name="c1" required>
                                                    @error('c1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif



                                        @if($server['settings'] == 'Twilio' || $server['settings'] == 'TwilioCopilot' || $server['settings'] == 'Plivo' || $server['settings'] == 'PlivoPowerpack'  || $server['settings'] == 'KarixIO' )
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="auth_token">Auth Token</label>
                                                    <input type="text" id="auth_token" class="form-control @error('auth_token') is-invalid @enderror" value="{{ $server['auth_token'] }}" name="auth_token" required>
                                                    @error('auth_token')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        @if($server['settings'] == 'ClickatellTouch' || $server['settings'] == 'ClickatellCentral' || $server['settings'] == 'TextLocal' || $server['settings'] == 'Vonage' || $server['settings'] == 'MessageBird' || $server['settings'] == 'Tyntec' || $server['settings'] == 'Telnyx' || $server['settings'] == 'Infobip' || $server['settings'] == 'BroadcasterMobile' || $server['settings'] == 'BeemAfrica' || $server['settings'] == 'ElitBuzzBD' || $server['settings'] == 'HablameV2' || $server['settings'] == 'ZamtelCoZm' || $server['settings'] == 'CellCast' || $server['settings'] == 'AfricasTalking' || $server['settings'] == 'SpoofSend' || $server['settings'] == 'AlhajSms' || $server['settings'] == 'SendroidUltimate' || $server['settings'] == 'RealSMS')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_key">API Key</label>
                                                    <input type="text" id="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ $server['api_key'] }}" name="api_key" required>
                                                    @error('api_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'Vonage' || $server['settings'] == 'Bandwidth' || $server['settings'] == 'BeemAfrica')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_secret">API Secret</label>
                                                    <input type="text" id="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ $server['api_secret'] }}" name="api_secret" required>
                                                    @error('api_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'msg91')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="auth_key">Auth Key</label>
                                                    <input type="text" id="auth_key" class="form-control @error('auth_key') is-invalid @enderror" value="{{ $server['auth_key'] }}" name="auth_key" required>
                                                    @error('auth_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'msg91')

                                            <div class="col-12">
                                                <fieldset class="form-group">
                                                    <label class="required" for="route">Route</label>
                                                    <select class="form-control" id="route" name="route">
                                                        <option value="1" {{ $server['route'] == '1' ? 'selected': null }}> Promotional</option>
                                                        <option value="4" {{ $server['route'] == '4' ? 'selected': null }}> Transactional</option>
                                                    </select>
                                                </fieldset>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'msg91')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="country_code">Country Code</label>
                                                    <input type="text" id="country_code" class="form-control @error('country_code') is-invalid @enderror" value="{{ $server['country_code'] }}" name="country_code" required>
                                                    @error('country_code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'SmsGatewayMe' || $server['settings'] == 'WhatsAppChatApi' || $server['settings'] == 'SignalWire' || $server['settings'] == 'Bandwidth' || $server['settings'] == 'BroadcasterMobile' || $server['settings'] == 'GreenWebBD' || $server['settings'] == 'HablameV2' || $server['settings'] == 'CaihCom'  || $server['settings'] == 'SpoofSend' || $server['settings'] == 'AlhajSms' || $server['settings'] == 'SendroidUltimate' || $server['settings'] == 'RealSMS')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_token">API Token</label>
                                                    <input type="text" id="api_token" class="form-control @error('api_token') is-invalid @enderror" value="{{ $server['api_token'] }}" name="api_token" required>
                                                    @error('api_token')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif


                                        @if($server['settings'] == 'SignalWire')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="project_id">Project ID</label>
                                                    <input type="text" id="project_id" class="form-control @error('project_id') is-invalid @enderror" value="{{ $server['project_id'] }}" name="project_id" required>
                                                    @error('project_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'SmsGatewayMe')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="device_id">Device ID</label>
                                                    <input type="text" id="device_id" class="form-control @error('device_id') is-invalid @enderror" value="{{ $server['device_id'] }}" name="device_id" required>
                                                    @error('device_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'AmazonSNS' || $server['settings'] == 'FlowRoute')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="access_key">Access Key</label>
                                                    <input type="text" id="access_key" class="form-control @error('access_key') is-invalid @enderror" value="{{ $server['access_key'] }}" name="access_key" required>
                                                    @error('access_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif


                                        @if($server['settings'] == 'FlowRoute')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_secret">Secret Key</label>
                                                    <input type="text" id="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ $server['api_secret'] }}" name="api_secret" required>
                                                    @error('api_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif


                                        @if($server['settings'] == 'AmazonSNS')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="secret_access">Secret Access</label>
                                                    <input type="text" id="secret_access" class="form-control @error('secret_access') is-invalid @enderror" value="{{ $server['secret_access'] }}" name="secret_access" required>
                                                    @error('secret_access')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'AmazonSNS')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="region">Region</label>
                                                    <input type="text" id="region" class="form-control @error('region') is-invalid @enderror" value="{{ $server['region'] }}" name="region" required>
                                                    @error('region')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'Bandwidth' || $server['settings'] == 'RouteeNet' || $server['settings'] == 'KeccelSMS')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="application_id">Application ID</label>
                                                    <input type="text" id="application_id" class="form-control @error('application_id') is-invalid @enderror" value="{{ $server['application_id'] }}" name="application_id" required>
                                                    @error('application_id')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif


                                        @if($server['settings'] == 'RouteeNet')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_secret">Application Secret</label>
                                                    <input type="text" id="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ $server['api_secret'] }}" name="api_secret" required>
                                                    @error('api_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'BroadcasterMobile')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c1">Country Code</label>
                                                    <input type="text" id="c1" class="form-control @error('c1') is-invalid @enderror" value="{{ $server['c1'] }}" name="c1" required>
                                                    @error('c1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'CaihCom')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c1">Channel Key</label>
                                                    <input type="text" id="c1" class="form-control @error('c1') is-invalid @enderror" value="{{ $server['c1'] }}" name="c1" required>
                                                    @error('c1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'HablameV2')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c1">Account</label>
                                                    <input type="text" id="c1" class="form-control @error('c1') is-invalid @enderror" value="{{ $server['c1'] }}" name="c1" required>
                                                    @error('c1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif


                                        @if($server['settings'] == 'AmazonSNS')

                                            <div class="col-12">
                                                <fieldset class="form-group">
                                                    <label class="required" for="sms_type">Message Type</label>
                                                    <select class="form-control" id="sms_type" name="sms_type">
                                                        <option value="Promotional" {{ $server['sms_type'] == 'Promotional' ? 'selected': null }}> Promotional</option>
                                                        <option value="Transactional" {{ $server['sms_type'] == 'Transactional' ? 'selected': null }}> Transactional</option>
                                                    </select>
                                                </fieldset>
                                            </div>

                                        @endif

                                        @if($server['settings'] == 'ClickatellCentral' || $server['settings'] == 'RouteMobile' || $server['settings'] == 'SMSGlobal' || $server['settings'] == 'BulkSMS' ||  $server['settings'] == '1s2u' || $server['settings'] == 'SMPP' || $server['settings'] == 'HutchLk' || $server['settings'] == 'Teletopiasms' || $server['settings'] == 'Solutions4mobiles' || $server['settings'] == 'BulkSMSOnline' || $server['settings'] == 'EasySendSMS' || $server['settings'] == 'AfricasTalking' || $server['settings'] == 'KeccelSMS' || $server['settings'] == 'Text2World')

                                            @if($server['settings'] != 'KeccelSMS')
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="required" for="username">User name</label>
                                                        <input type="text" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ $server['username'] }}" name="username" required>
                                                        @error('username')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif

                                            @if($server['settings'] != 'AfricasTalking')
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="required" for="password">Password</label>
                                                        <input type="text" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ $server['password'] }}" name="password" required>
                                                        @error('password')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                        @endif


                                        @if($server['settings'] == 'CheapGlobalSMS')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="username">Sub Account</label>
                                                    <input type="text" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ $server['username'] }}" name="username" required>
                                                    @error('username')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="password">Sub Account Password</label>
                                                    <input type="text" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ $server['password'] }}" name="password" required>
                                                    @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        @if($server['settings'] == 'JohnsonConnect')
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_key">App Key</label>
                                                    <input type="text" id="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ $server['api_key'] }}" name="api_key" required>
                                                    @error('api_key')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="api_secret">Secret Key</label>
                                                    <input type="text" id="api_secret" class="form-control @error('api_secret') is-invalid @enderror" value="{{ $server['api_secret'] }}" name="api_secret" required>
                                                    @error('api_secret')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif


                                        @if($server['settings'] == 'WaApi')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c1">Client ID</label>
                                                    <input type="text" id="c1" class="form-control @error('c1') is-invalid @enderror" value="{{ $server['c1'] }}" name="c1" required>
                                                    @error('c1')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="c2">Instance ID</label>
                                                    <input type="text" id="c2" class="form-control @error('c2') is-invalid @enderror" value="{{ $server['c2'] }}" name="c2" required>
                                                    @error('c2')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        @endif



                                        @if($server['settings'] == 'SMPP')

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="port">Port</label>
                                                    <input type="text" id="port" class="form-control @error('port') is-invalid @enderror" value="{{ $server['port'] }}" name="port" required>
                                                    @error('port')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="source_addr_ton">Source Address Ton</label>
                                                    <input type="text" id="source_addr_ton" class="form-control @error('source_addr_ton') is-invalid @enderror" value="{{ $server['source_addr_ton'] }}" name="source_addr_ton" required>
                                                    @error('source_addr_ton')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="source_addr_npi">Source Address NPI</label>
                                                    <input type="text" id="source_addr_npi" class="form-control @error('source_addr_npi') is-invalid @enderror" value="{{ $server['source_addr_npi'] }}" name="source_addr_npi" required>
                                                    @error('source_addr_npi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="dest_addr_ton">Destination Address Ton</label>
                                                    <input type="text" id="dest_addr_ton" class="form-control @error('dest_addr_ton') is-invalid @enderror" value="{{ $server['dest_addr_ton'] }}" name="dest_addr_ton" required>
                                                    @error('dest_addr_ton')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="required" for="dest_addr_npi">Destination Address NPI</label>
                                                    <input type="text" id="dest_addr_npi" class="form-control @error('dest_addr_npi') is-invalid @enderror" value="{{ $server['dest_addr_npi'] }}" name="dest_addr_npi" required>
                                                    @error('dest_addr_npi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                        @endif

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{--Sending Speed and per request sms--}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"> {{__('locale.sending_servers.sending_limit')}} </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <p>{!! __('locale.description.sending_credit') !!} </p>
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="quota_value">{{__('locale.sending_servers.sending_limit')}}</label>
                                                <input type="number" id="quota_value" class="form-control @error('quota_value') is-invalid @enderror" value="{{ $server['quota_value'] }}" name="quota_value" required>
                                                @error('quota_value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="quota_base">{{__('locale.sending_servers.time_base')}}</label>
                                                <input type="number" id="quota_base" class="form-control @error('quota_base') is-invalid @enderror" value="{{ $server['quota_base'] }}" name="quota_base" required>
                                                @error('quota_base')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <fieldset class="form-group">
                                                <label class="required" for="quota_unit">{{__('locale.sending_servers.time_unit')}}</label>
                                                <select class="form-control" id="quota_unit" name="quota_unit">
                                                    <option value="minute" {{ $server['quota_unit'] == 'minute' ? 'selected': null }}> {{__('locale.labels.minute')}}</option>
                                                    <option value="hour" {{ $server['quota_unit'] == 'hour' ? 'selected': null }}>  {{__('locale.labels.hour')}}</option>
                                                    <option value="day" {{ $server['quota_unit'] == 'day' ? 'selected': null }}>  {{__('locale.labels.day')}}</option>
                                                </select>
                                            </fieldset>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="sms_per_request">{{__('locale.sending_servers.per_single_request')}}</label>
                                                <input type="number" id="sms_per_request" class="form-control @error('sms_per_request') is-invalid @enderror" value="{{ $server['sms_per_request'] }}" name="sms_per_request" required>
                                                @error('sms_per_request')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{--All Predefine features listed here--}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                {{__('locale.sending_servers.available_features')}}
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                <div class="form-body">
                                    <div class="row">

                                        <div class="d-flex justify-content-start flex-wrap col-12">

                                            @if($server['type'] != 'whatsapp')
                                                <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                    <p class="mb-0">{{__('locale.labels.plain')}}</p>
                                                    <input type="hidden" value="0" name="plain">
                                                    <input type="checkbox" class="custom-control-input" value="1" name="plain" id="plain" {{ $server['plain'] ? 'checked': null }}>
                                                    <label class="custom-control-label" for="plain">
                                                        <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                        <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                    </label>
                                                </div>
                                            @endif


                                            <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                <p class="mb-0">{{__('locale.labels.schedule')}}</p>
                                                <input type="hidden" value="0" name="schedule">
                                                <input type="checkbox" class="custom-control-input" value="1" id="schedule" name="schedule" {{ $server['schedule'] ? 'checked': null }}>
                                                <label class="custom-control-label" for="schedule">
                                                    <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                    <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                </label>
                                            </div>


                                            @if($server['settings'] == 'Twilio' || $server['settings'] == 'Plivo' || $server['settings'] == 'Vonage' || $server['settings'] == 'Infobip' || $server['settings'] == 'MessageBird')

                                                <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                    <p class="mb-0">{{__('locale.labels.voice')}}</p>
                                                    <input type="hidden" value="0" name="voice">
                                                    <input type="checkbox" class="custom-control-input" value="1" id="voice" name="voice" {{ $server['voice'] ? 'checked': null }}>
                                                    <label class="custom-control-label" for="voice">
                                                        <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                        <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                    </label>
                                                </div>

                                            @endif

                                            @if($server['settings'] == 'Twilio' || $server['settings'] == 'TextLocal' || $server['settings'] == 'Plivo' || $server['settings'] == 'SMSGlobal' || $server['settings'] == 'MessageBird' || $server['settings'] == 'WhatsAppChatApi' || $server['settings'] == 'SignalWire' || $server['settings'] == 'Telnyx' || $server['settings'] == 'Bandwidth' || $server['settings'] == 'WaApi')

                                                <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                    <p class="mb-0">{{__('locale.labels.mms')}}</p>
                                                    <input type="hidden" value="0" name="mms">
                                                    <input type="checkbox" class="custom-control-input" value="1" name="mms" id="mms" {{ $server['mms'] ? 'checked': null }}>
                                                    <label class="custom-control-label" for="mms">
                                                        <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                        <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                    </label>
                                                </div>

                                            @endif

                                            @if($server['settings'] == 'Twilio' || $server['settings'] == 'TwilioCopilot' || $server['settings'] == 'Clickatell_Touch' || $server['settings'] == 'Clickatell_central' || $server['settings'] == 'TextLocal' || $server['settings'] == 'Plivo' || $server['settings'] == 'PlivoPowerpack' || $server['settings'] == 'BulkSMS' || $server['settings'] == 'SMSGlobal' || $server['settings'] == 'Vonage' || $server['settings'] == 'MessageBird' || $server['settings'] == 'WhatsAppChatApi' || $server['settings'] == 'SignalWire' || $server['settings'] == 'Telnyx' || $server['settings'] == 'Bandwidth' || $server['settings'] == 'Infobip' || $server['settings'] == 'Tyntec' || $server['settings'] == 'EasySendSMS')

                                                <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                    <p class="mb-0">{{__('locale.labels.two_way')}}</p>
                                                    <input type="hidden" value="0" name="two_way">
                                                    <input type="checkbox" class="custom-control-input" value="1" name="two_way" id="two_way" {{ $server['two_way'] ? 'checked': null }}>
                                                    <label class="custom-control-label" for="two_way">
                                                        <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                        <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                    </label>
                                                </div>

                                            @endif

                                            @if($server['settings'] == 'Twilio' || $server['settings'] == 'Clickatell_Touch' || $server['settings'] == 'MessageBird' || $server['settings'] == 'WhatsAppChatApi')
                                                <div class="custom-control custom-switch switch-lg custom-switch-success mr-2 mb-1">
                                                    <p class="mb-0">{{__('locale.labels.whatsapp')}}</p>
                                                    <input type="hidden" value="0" name="whatsapp">
                                                    <input type="checkbox" class="custom-control-input" value="1" id="whatsapp" name="whatsapp" {{ $server['whatsapp'] ? 'checked': null }}>
                                                    <label class="custom-control-label" for="whatsapp">
                                                        <span class='switch-text-left'>{{ __('locale.labels.active') }}</span>
                                                        <span class='switch-text-right'>{{ __('locale.labels.inactive') }}</span>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 mt-2">
                                            <input type="hidden" name="settings" value="{{$server['settings']}}">
                                            <input type="hidden" name="type" value="{{$server['type']}}">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="feather icon-save"></i> {{__('locale.buttons.save')}} </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </form>

            </div>
        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection
