<div class="row">
    <div class="col-md-8 col-12">
        <p>{{__('locale.description.plan_sending_server_intro')}}.</p>
    </div>
    <div class="col-md-4 col-12">
        <button class="btn btn-primary px-1 py-1 waves-effect waves-light pull-right btn-sm" data-toggle="modal"
                data-target="#addSendingSever"><i
                    class="feather icon-plus-circle"></i> {{__('locale.sending_servers.add_sending_server')}}
        </button>


        {{-- Modal --}}
        <div class="modal fade text-left" id="addSendingSever" role="dialog" aria-labelledby="addSendingSever"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">{{ __('locale.sending_servers.add_sending_server') }} </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.plans.settings.sending-servers', $plan->uid) }}" method="post">
                        @csrf
                        <div class="modal-body">

                            <label for="sending_server_uid" class="required">{{__('locale.sending_servers.select_sending_server')}}</label>
                            <div class="form-group">

                                <select class="form-control select2" id="sending_server_id" name="sending_server_id">
                                    @if($sending_servers->count())
                                        @foreach($sending_servers as $server)
                                            <option value="{{$server->uid}}"> {{ $server->name }}</option>
                                        @endforeach
                                    @else
                                        <option>{{ __('locale.sending_servers.have_no_sending_server_to_add') }}</option>
                                    @endif
                                </select>
                                @error('sending_server_id')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('locale.buttons.close')}}</button>
                            <button type="submit" class="btn btn-primary btn-sm">{{__('locale.labels.choose')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- DataTable starts --}}
    <div class="table-responsive mt-4">
        <table class="table table-striped data-list-view">
            <tbody>
            @if($plan->plansSendingServers()->count() > 0)
                @foreach ($plan->plansSendingServers as $planSendingServer)
                    @if(! empty($planSendingServer->sendingServer))
                        <tr>
                            <td>
                                <div>
                                    <p>{{ $planSendingServer->sendingServer->name }}
                                        @if ($planSendingServer->isPrimary())
                                            <span class="badge badge-primary">{{__('locale.labels.primary')}}</span>
                                        @endif
                                    </p>
                                    <p class="text-muted">{{__('locale.sending_servers.sending_limit')}} {!! $planSendingServer->sendingServer->displayQuotaHtml() !!}</p>
                                </div>
                            </td>
                            <td class="text-right">
                                @if (!$planSendingServer->isPrimary())
                                    <button class="btn btn-primary btn-sm  action-set-primary mr-1"
                                            data-id="{{ $planSendingServer->sendingServer->uid }}"><i
                                                class="feather icon-save"></i> {{ __('locale.labels.set_primary') }}</button>
                                @endif

                                <a href="{{ route('admin.sending-servers.show', $planSendingServer->sendingServer->uid) }}" class="btn btn-success btn-sm "><i class="feather icon-edit"></i> {{__('locale.buttons.edit')}}</a>

                                <button class="btn btn-danger  btn-sm action-delete mr-1" data-id="{{ $planSendingServer->sendingServer->uid }}"><i class="feather icon-trash"></i> {{ __('locale.buttons.delete') }}</button>


                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                <p class="text-center text-highlight text-bold-600 text-danger"><i
                            class="feather icon-menu"></i> {{ __('locale.sending_servers.have_no_sending_server') }}
                </p>
            @endif
            </tbody>
        </table>
    </div>

    {{-- DataTable ends --}}

    {{--    --}}{{--Update fitness modal --}}

    {{--    <div class="modal fade text-left" id="updateFitness" role="dialog" aria-labelledby="updateFitness" aria-hidden="true">--}}
    {{--        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">--}}
    {{--            <div class="modal-content">--}}
    {{--                <div class="modal-header">--}}
    {{--                    <h4 class="modal-title" id="updateFitness">{{ __('locale.labels.fitness') }} </h4>--}}
    {{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
    {{--                        <span aria-hidden="true">&times;</span>--}}
    {{--                    </button>--}}
    {{--                </div>--}}
    {{--                <form action="{{ route('admin.plans.settings.update-fitness', $plan->uid) }}" method="post">--}}
    {{--                    @csrf--}}
    {{--                    <div class="modal-body">--}}

    {{--                        @foreach ($plan->plansSendingServers as $planSendingServer)--}}
    {{--                            <fieldset class="mt-2">--}}
    {{--                                <div class="form-group">--}}
    {{--                                    <label class="text-bold-600 text-primary">{{$planSendingServer->sendingServer->name}}</label>--}}
    {{--                                    <div class="square slider-md slider-primary my-1 sliders" data-value="{{ $planSendingServer->fitness }}"  data-id="{{ $planSendingServer->sendingServer->uid }}" >--}}
    {{--                                        <input name="sending_servers[{{ $planSendingServer->sendingServer->uid }}]" value="" class="slider" id="slider_{{ $planSendingServer->sendingServer->uid }}" type="hidden"/>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </fieldset>--}}
    {{--                        @endforeach--}}

    {{--                    </div>--}}
    {{--                    <div class="modal-footer">--}}
    {{--                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">{{__('locale.buttons.close')}}</button>--}}
    {{--                        <button type="submit" class="btn btn-primary btn-sm">{{__('locale.buttons.update')}}</button>--}}
    {{--                    </div>--}}
    {{--                </form>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    {{--    --}}{{--Update fitness modal end--}}

</div>

