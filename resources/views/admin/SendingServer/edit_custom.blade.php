@extends('layouts/contentLayoutMaster')

@section('title', __('locale.sending_servers.create_own_server'))


@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">

        <form class="form form-horizontal" action="{{route('admin.sending-servers.update.custom', $server['uid'])}}" method="post">
            @csrf
            <div class="row">

                <div class="col-md-4 col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title">{{ __('locale.sending_servers.create_own_server') }}</h4>
                        </div>


                        <div class="card-content">
                            <div class="card-body">

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

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="api_link">Base URL</label>
                                                <input type="text" id="api_link" class="form-control @error('api_link') is-invalid @enderror" value="{{ $server['api_link'] }}" name="api_link" required>
                                                @error('api_link')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="success_keyword">{{__('locale.labels.success_keyword')}}</label>
                                                <input type="text" id="success_keyword" class="form-control @error('success_keyword') is-invalid @enderror" value="{{ $server['success_keyword'] }}" name="success_keyword" required>
                                                @error('success_keyword')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="http_request_method">{{__('locale.labels.http_request_method')}}</label>
                                                <select class="form-control" id="http_request_method" name="http_request_method">
                                                    <option value="get" {{ $data['http_request_method'] == 'get' ? 'selected': null }}>GET</option>
                                                    <option value="post" {{ $data['http_request_method'] == 'post' ? 'selected': null }}>POST</option>
                                                </select>
                                                @error('http_request_method')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="json_encoded_post"> {{__('locale.labels.json_encoded_post')}} </label>
                                                <select class="form-control" id="json_encoded_post" name="json_encoded_post">
                                                    <option value="0"  {{ $data['json_encoded_post'] == '0' ? 'selected': null }}>{{__('locale.labels.no')}}</option>
                                                    <option value="1"  {{ $data['json_encoded_post'] == '1' ? 'selected': null }}>{{__('locale.labels.yes')}}</option>
                                                </select>
                                                @error('json_encoded_post')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="content_type"> {{__('locale.labels.content_type')}} </label>
                                                <select class="form-control" id="content_type" name="content_type">
                                                    <option value="none" {{ $data['content_type'] == 'none' ? 'selected': null }}>{{__('locale.labels.none')}}</option>
                                                    <option value="application/json" {{ $data['content_type'] == 'application/json' ? 'selected': null }}>application/json</option>
                                                    <option value="application/x-www-form-urlencoded" {{ $data['content_type'] == 'application/x-www-form-urlencoded' ? 'selected': null }}>application/x-www-form-urlencoded</option>
                                                </select>
                                                @error('content_type')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="content_type_accept"> {{__('locale.labels.content_type_accept')}} </label>
                                                <select class="form-control" id="content_type_accept" name="content_type_accept">
                                                    <option value="none" {{ $data['content_type_accept'] == 'none' ? 'selected': null }}>{{__('locale.labels.none')}}</option>
                                                    <option value="application/json" {{ $data['content_type_accept'] == 'application/json' ? 'selected': null }}>application/json</option>
                                                    <option value="application/x-www-form-urlencoded" {{ $data['content_type_accept'] == 'application/x-www-form-urlencoded' ? 'selected': null }}>application/x-www-form-urlencoded</option>
                                                </select>
                                                @error('content_type_accept')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="character_encoding"> {{__('locale.labels.character_encoding')}} </label>
                                                <select class="form-control" id="character_encoding" name="character_encoding">
                                                    <option value="none" {{ $data['character_encoding'] == 'none' ? 'selected': null }}>{{__('locale.labels.none')}}</option>
                                                    <option value="gsm-7" {{ $data['character_encoding'] == 'gsm-7' ? 'selected': null }}>gsm-7</option>
                                                    <option value="ucs-2" {{ $data['character_encoding'] == 'ucs-2' ? 'selected': null }}>ucs-2</option>
                                                    <option value="utf-8" {{ $data['character_encoding'] == 'utf-8' ? 'selected': null }}>utf-8</option>
                                                    <option value="utf-16" {{ $data['character_encoding'] == 'utf-16' ? 'selected': null }}>utf-16</option>
                                                    <option value="utf-32" {{ $data['character_encoding'] == 'utf-32' ? 'selected': null }}>utf-32</option>
                                                    <option value="iso-8859-1" {{ $data['character_encoding'] == 'iso-8859-1' ? 'selected': null }}>iso-8859-1</option>
                                                    <option value="ucs-2be" {{ $data['character_encoding'] == 'ucs-2be' ? 'selected': null }}>ucs-2be</option>
                                                </select>
                                                @error('character_encoding')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="ssl_certificate_verification"> {{__('locale.labels.ssl_certificate_verification')}} </label>
                                                <select class="form-control" id="ssl_certificate_verification" name="ssl_certificate_verification">
                                                    <option value="0"  {{ $data['ssl_certificate_verification'] == '0' ? 'selected': null }}>{{__('locale.labels.no')}}</option>
                                                    <option value="1"  {{ $data['ssl_certificate_verification'] == '1' ? 'selected': null }}>{{__('locale.labels.yes')}}</option>
                                                </select>
                                                @error('ssl_certificate_verification')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="authorization"> {{__('locale.labels.authorization')}} </label>
                                                <select class="form-control" id="authorization" name="authorization">
                                                    <option value="no_auth" {{ $data['authorization'] == 'no_auth' ? 'selected': null }}>{{__('locale.sending_servers.no_auth')}}</option>
                                                    <option value="bearer_token" {{ $data['authorization'] == 'bearer_token' ? 'selected': null }}>Bearer Token</option>
                                                    <option value="basic_auth" {{ $data['authorization'] == 'basic_auth' ? 'selected': null }}>Basic Auth</option>
                                                </select>
                                                @error('authorization')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="plain">{{__('locale.labels.plain')}}</label>
                                                <select class="form-control" id="plain" name="plain">
                                                    <option value="1"  {{ $server['plain'] == '1' ? 'selected': null }}>{{__('locale.labels.yes')}}</option>
                                                    <option value="0"  {{ $server['plain'] == '0' ? 'selected': null }}>{{__('locale.labels.no')}}</option>
                                                </select>
                                                @error('plain')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="schedule">{{__('locale.labels.schedule')}}</label>
                                                <select class="form-control" id="schedule" name="schedule">
                                                    <option value="1"  {{ $server['schedule'] == '1' ? 'selected': null }}>{{__('locale.labels.yes')}}</option>
                                                    <option value="0"  {{ $server['schedule'] == '0' ? 'selected': null }}>{{__('locale.labels.no')}}</option>
                                                </select>
                                                @error('schedule')
                                                <div class="text-danger">
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

                    {{--Sending Speed and per request sms--}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"> {{__('locale.sending_servers.sending_credit')}} </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <p>{!! __('locale.description.custom_sending_credit') !!} </p>
                                <div class="form-body">
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="quota_value">{{__('locale.sending_servers.sending_credit')}}</label>
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
                                            <div class="form-group">
                                                <label class="required" for="quota_unit">{{__('locale.sending_servers.time_unit')}}</label>
                                                <select class="form-control" id="quota_unit" name="quota_unit">
                                                    <option value="minute" {{ $server['quota_unit'] == 'minute' ? 'selected': null }}> {{__('locale.labels.minute')}}</option>
                                                    <option value="hour" {{ $server['quota_unit'] == 'hour' ? 'selected': null }}>  {{__('locale.labels.hour')}}</option>
                                                    <option value="day" {{ $server['quota_unit'] == 'day' ? 'selected': null }}>  {{__('locale.labels.day')}}</option>
                                                </select>
                                                @error('quota_unit')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
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

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="required" for="multi_sms_delimiter">{{__('locale.sending_servers.delimiter_multiple_sms')}}</label>
                                                <select class="form-control" id="multi_sms_delimiter" name="multi_sms_delimiter">
                                                    <option value=","  {{ $data['multi_sms_delimiter'] == ',' ? 'selected': null }}>, (comma)</option>
                                                    <option value=";"  {{ $data['multi_sms_delimiter'] == ';' ? 'selected': null }}>; (semi-colon)</option>
                                                    <option value="array"  {{ $data['multi_sms_delimiter'] == 'array' ? 'selected': null }}>array()</option>
                                                </select>
                                                @error('multi_sms_delimiter')
                                                <div class="text-danger">
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
                </div>

                <div class="col-md-8 col-12">
                    <div class="card">

                        <div class="card-header">
                            <h4 class="card-title"> {{__('locale.sending_servers.query_parameters')}} </h4>
                            <button class="btn btn-primary pull-right" type="submit"><i class="feather icon-save"></i> {{__('locale.buttons.update')}}</button>
                        </div>

                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="gateway_items">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{__('locale.sending_servers.parameter')}}</th>
                                                <th>{{__('locale.sending_servers.value')}}</th>
                                                <th>{{__('locale.sending_servers.add_on_url')}}</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                    <tr>
                                        <td>{{__('locale.labels.username')}} / {{ __('locale.labels.api_key') }}</td>
                                        <td><input type="text" autocomplete="off" required name="username_param" value="{{$data['username_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" required name="username_value" value="{{$data['username_value']}}" class="form-control"></td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.password')}}</td>
                                        <td><input type="text" autocomplete="off" name="password_param"  value="{{$data['password_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="password_value"  value="{{$data['password_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="password_status">
                                                <option value="0" {{ $data['password_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['password_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.action')}}</td>
                                        <td><input type="text" autocomplete="off" name="action_param" value="{{$data['action_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="action_value" value="{{$data['action_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="action_status">
                                                <option value="0" {{ $data['action_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['action_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.source')}}</td>
                                        <td><input type="text" autocomplete="off" name="source_param" value="{{$data['source_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="source_value" value="{{$data['source_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="source_status">
                                                <option value="0" {{ $data['source_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['source_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.destination')}}</td>
                                        <td><input type="text" autocomplete="off" required name="destination_param" value="{{$data['destination_param']}}" class="form-control"></td>
                                        <td></td>
                                        <td></td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.message')}}</td>
                                        <td><input type="text" autocomplete="off" required name="message_param" value="{{$data['message_param']}}" class="form-control"></td>
                                        <td></td>
                                        <td></td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.unicode')}}</td>
                                        <td><input type="text" autocomplete="off" name="unicode_param" value="{{$data['unicode_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="unicode_value" value="{{$data['unicode_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="unicode_status">
                                                <option value="0" {{ $data['unicode_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['unicode_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.type')}} / {{__('locale.labels.route')}}</td>
                                        <td><input type="text" autocomplete="off" name="route_param" value="{{$data['route_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="route_value" value="{{$data['route_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="route_status">
                                                <option value="0" {{ $data['route_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['route_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.language')}}</td>
                                        <td><input type="text" autocomplete="off" name="language_param" value="{{$data['language_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="language_value" value="{{$data['language_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="language_status">
                                                <option value="0" {{ $data['language_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['language_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 1</td>
                                        <td><input type="text" autocomplete="off" name="custom_one_param" value="{{$data['custom_one_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_one_value" value="{{$data['custom_one_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_one_status">
                                                <option value="0" {{ $data['custom_one_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['custom_one_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 2</td>
                                        <td><input type="text" autocomplete="off" name="custom_two_param" value="{{$data['custom_two_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_two_value" value="{{$data['custom_two_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_two_status">
                                                <option value="0" {{ $data['custom_two_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['custom_two_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 3</td>
                                        <td><input type="text" autocomplete="off" name="custom_three_param" value="{{$data['custom_three_param']}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_three_value" value="{{$data['custom_three_value']}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_three_status">
                                                <option value="0" {{ $data['custom_three_status'] == '0' ? 'selected': null }}>{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1" {{ $data['custom_three_status'] == '1' ? 'selected': null }}>{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>


                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection
