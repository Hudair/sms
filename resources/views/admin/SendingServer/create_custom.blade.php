@extends('layouts/contentLayoutMaster')

@section('title', __('locale.sending_servers.create_own_server'))


@section('content')
    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">

        <form class="form form-horizontal" action="{{route('admin.sending-servers.add.custom')}}" method="post">
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
                                                <label for="name" class="required">{{ __('locale.labels.name') }}</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" name="name" required>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="api_link" class="required">Base URL</label>
                                                <input type="text" id="api_link" class="form-control @error('api_link') is-invalid @enderror" value="{{ old('api_link') }}" name="api_link" required>
                                                @error('api_link')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="success_keyword" class="required">{{__('locale.labels.success_keyword')}}</label>
                                                <input type="text" id="success_keyword" class="form-control @error('success_keyword') is-invalid @enderror" value="{{ old('success_keyword') }}" name="success_keyword" required>
                                                @error('success_keyword')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="http_request_method" class="required">{{__('locale.labels.http_request_method')}}</label>
                                                <select class="form-control" id="http_request_method" name="http_request_method">
                                                    <option value="get">GET</option>
                                                    <option value="post">POST</option>
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
                                                    <option value="0">{{__('locale.labels.no')}}</option>
                                                    <option value="1">{{__('locale.labels.yes')}}</option>
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
                                                    <option value="none">{{__('locale.labels.none')}}</option>
                                                    <option value="application/json">application/json</option>
                                                    <option value="application/x-www-form-urlencoded">application/x-www-form-urlencoded</option>
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
                                                    <option value="none">{{__('locale.labels.none')}}</option>
                                                    <option value="application/json">application/json</option>
                                                    <option value="application/x-www-form-urlencoded">application/x-www-form-urlencoded</option>
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
                                                    <option value="none">{{__('locale.labels.none')}}</option>
                                                    <option value="gsm-7">gsm-7</option>
                                                    <option value="ucs-2">ucs-2</option>
                                                    <option value="utf-8">utf-8</option>
                                                    <option value="utf-16">utf-16</option>
                                                    <option value="utf-32">utf-32</option>
                                                    <option value="iso-8859-1">iso-8859-1</option>
                                                    <option value="ucs-2be">ucs-2be</option>
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
                                                    <option value="1">{{__('locale.labels.yes')}}</option>
                                                    <option value="0">{{__('locale.labels.no')}}</option>
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
                                                <label for="authorization" class="required"> {{__('locale.labels.authorization')}} </label>
                                                <select class="form-control" id="authorization" name="authorization">
                                                    <option value="no_auth">{{__('locale.sending_servers.no_auth')}}</option>
                                                    <option value="bearer_token">Bearer Token</option>
                                                    <option value="basic_auth">Basic Auth</option>
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
                                                <label for="plain" class="required">{{__('locale.labels.plain')}}</label>
                                                <select class="form-control" id="plain" name="plain">
                                                    <option value="1"> {{__('locale.labels.yes')}}</option>
                                                    <option value="0">  {{__('locale.labels.no')}}</option>
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
                                                <label for="schedule" class="required">{{__('locale.labels.schedule')}}</label>
                                                <select class="form-control" id="schedule" name="schedule">
                                                    <option value="1"> {{__('locale.labels.yes')}}</option>
                                                    <option value="0">  {{__('locale.labels.no')}}</option>
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
                                                <label for="quota_value" class="required">{{__('locale.sending_servers.sending_credit')}}</label>
                                                <input type="number" id="quota_value" class="form-control @error('quota_value') is-invalid @enderror" value="60" name="quota_value" required>
                                                @error('quota_value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="quota_base" class="required">{{__('locale.sending_servers.time_base')}}</label>
                                                <input type="number" id="quota_base" class="form-control @error('quota_base') is-invalid @enderror" value="1" name="quota_base" required>
                                                @error('quota_base')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="quota_unit" class="required">{{__('locale.sending_servers.time_unit')}}</label>
                                                <select class="form-control" id="quota_unit" name="quota_unit">
                                                    <option value="minute"> {{__('locale.labels.minute')}}</option>
                                                    <option value="hour">  {{__('locale.labels.hour')}}</option>
                                                    <option value="day">  {{__('locale.labels.day')}}</option>
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
                                                <label for="sms_per_request" class="required">{{__('locale.sending_servers.per_single_request')}}</label>
                                                <input type="number" id="sms_per_request" class="form-control @error('sms_per_request') is-invalid @enderror" value="1" name="sms_per_request" required>
                                                @error('sms_per_request')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="multi_sms_delimiter" class="required">{{__('locale.sending_servers.delimiter_multiple_sms')}}</label>
                                                <select class="form-control" id="multi_sms_delimiter" name="multi_sms_delimiter">
                                                    <option value=",">, (comma)</option>
                                                    <option value=";">; (semi-colon)</option>
                                                    <option value="array">array()</option>
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
                            <button class="btn btn-primary pull-right" type="submit"><i class="feather icon-plus-circle"></i> {{__('locale.buttons.save')}}</button>
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
                                        <td><input type="text" autocomplete="off" required name="username_param" value="{{old('username_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" required name="username_value" value="{{old('username_value')}}" class="form-control"></td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.password')}}</td>
                                        <td><input type="text" autocomplete="off" name="password_param"  value="{{old('password_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="password_value"  value="{{old('password_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="password_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.action')}}</td>
                                        <td><input type="text" autocomplete="off" name="action_param" value="{{old('action_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="action_value" value="{{old('action_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="action_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.source')}}</td>
                                        <td><input type="text" autocomplete="off" name="source_param" value="{{old('source_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="source_value" value="{{old('source_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="source_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.destination')}}</td>
                                        <td><input type="text" autocomplete="off" required name="destination_param" value="{{old('destination_param')}}" class="form-control"></td>
                                        <td></td>
                                        <td></td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.message')}}</td>
                                        <td><input type="text" autocomplete="off" required name="message_param" value="{{old('message_param')}}" class="form-control"></td>
                                        <td></td>
                                        <td></td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.unicode')}}</td>
                                        <td><input type="text" autocomplete="off" name="unicode_param" value="{{old('unicode_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="unicode_value" value="{{old('unicode_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="unicode_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.type')}} / {{__('locale.labels.route')}}</td>
                                        <td><input type="text" autocomplete="off" name="route_param" value="{{old('route_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="route_value" value="{{old('route_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="route_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td>{{__('locale.labels.language')}}</td>
                                        <td><input type="text" autocomplete="off" name="language_param" value="{{old('language_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="language_value" value="{{old('language_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="language_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 1</td>
                                        <td><input type="text" autocomplete="off" name="custom_one_param" value="{{old('custom_one_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_one_value" value="{{old('custom_one_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_one_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 2</td>
                                        <td><input type="text" autocomplete="off" name="custom_two_param" value="{{old('custom_two_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_two_value" value="{{old('custom_two_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_two_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
                                            </select>
                                        </td>

                                    <tr>
                                        <td>{{__('locale.labels.custom_value')}} 3</td>
                                        <td><input type="text" autocomplete="off" name="custom_three_param" value="{{old('custom_three_param')}}" class="form-control"></td>
                                        <td><input type="text" autocomplete="off" name="custom_three_value" value="{{old('custom_three_value')}}" class="form-control"></td>
                                        <td>
                                            <select class="form-control" name="custom_three_status">
                                                <option value="0">{{__('locale.sending_servers.set_blank')}}</option>
                                                <option value="1">{{__('locale.sending_servers.add_on_parameter')}}</option>
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
