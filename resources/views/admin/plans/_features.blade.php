<div class="col-md-8 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.plans.settings.features', $plan->uid) }}" method="post">
            @csrf
            <div class="row">

                {{-- SMS sending credits --}}
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label class="required">{{__('locale.plans.sms_sending_credits')}}</label>
                        <input type="number" name="sms_max" class="form-control text-right sms-max-input @error('sms_max') is-invalid @enderror"
                                {{ $options['sms_max'] == '-1' ? 'disabled': 'value='.$options['sms_max'] }}
                        >
                        @error('sms_max')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12 mt-md-2 mt-0">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" id="sms_max" value="-1" name="sms_max"
                                    {{ $options['sms_max'] == '-1' ? 'checked': null }}
                            >
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.unlimited')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- Max contact lists --}}
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>{{__('locale.plans.max_contact_list')}}</label>
                        <input type="number" name="list_max" class="form-control text-right list-max-input @error('list_max') is-invalid @enderror"
                                {{ $options['list_max'] == '-1' ? 'disabled': 'value='.$options['list_max'] }}
                        >
                        @error('list_max')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12 mt-md-2 mt-0">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" id="list_max" value="-1" name="list_max"
                                    {{ $options['list_max'] == '-1' ? 'checked': null }}
                            >
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.unlimited')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- Max Subscribers --}}
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>{{__('locale.plans.max_contact')}}</label>
                        <input type="number" name="subscriber_max" class="form-control text-right subscriber-max-input @error('subscriber_max') is-invalid @enderror"
                                {{ $options['subscriber_max'] == '-1' ? 'disabled': 'value='.$options['subscriber_max'] }}
                        >
                        @error('subscriber_max')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12 mt-md-2 mt-0">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" id="subscriber_max" value="-1" name="subscriber_max"
                                    {{ $options['subscriber_max'] == '-1' ? 'checked': null }}
                            >
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.unlimited')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- Max Subscribers per list--}}
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>{{__('locale.plans.max_contact_per_list')}}</label>
                        <input type="number" name="subscriber_per_list_max" class="form-control  text-right subscriber-per-list-max-input @error('subscriber_per_list_max') is-invalid @enderror"
                                {{ $options['subscriber_per_list_max'] == '-1' ? 'disabled': 'value='.$options['subscriber_per_list_max'] }}
                        >
                        @error('subscriber_per_list_max')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12 mt-md-2 mt-0">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" id="subscriber_per_list_max" value="-1" name="subscriber_per_list_max"
                                    {{ $options['subscriber_per_list_max'] == '-1' ? 'checked': null }}
                            >
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.unlimited')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- Max segments per list--}}
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>{{__('locale.plans.segment_per_list_max')}}</label>
                        <input type="number" name="segment_per_list_max" class="form-control  text-right segment-per-list-max-input @error('segment_per_list_max') is-invalid @enderror"
                                {{ $options['segment_per_list_max'] == '-1' ? 'disabled': 'value='.$options['segment_per_list_max'] }}
                        >
                        @error('segment_per_list_max')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 col-12 mt-md-2 mt-0">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" id="segment_per_list_max" value="-1" name="segment_per_list_max"
                                    {{ $options['segment_per_list_max'] == '-1' ? 'checked': null }}
                            >
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.unlimited')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- can import list--}}
                <div class="col-12">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="list_import" {{ $options['list_import'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_import_list')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- can export list--}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="list_export" {{ $options['list_export'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_export_list')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- can access api--}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="api_access" {{ $options['api_access'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_use_api')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- create own sending server --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="create_sending_server" {{ $options['create_sending_server'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_own_sending_server')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- customer can create sub account --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="create_sub_account" {{ $options['create_sub_account'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_create_sub_accounts')}}</span>
                        </div>
                    </fieldset>
                </div>


                {{-- customer can delete sms history --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="delete_sms_history" {{ $options['delete_sms_history'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.customer_can_delete_sms_history')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- sender id verification --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="sender_id_verification" {{ $options['sender_id_verification'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.need_sender_id_verification')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- send spam messages --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="send_spam_message" {{ $options['send_spam_message'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.send_spam_message')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- cutting system avilable --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="cutting_system" {{ $options['cutting_system'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.cutting_system_available')}}</span>
                        </div>
                    </fieldset>
                </div>

                {{-- add_previous_balance --}}
                <div class="col-12 mt-md-1">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="yes" name="add_previous_balance" {{ $options['add_previous_balance'] == 'yes' ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.add_previous_balance_next_subscription')}}</span>
                        </div>
                    </fieldset>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary mr-1 mb-1">
                        <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                    </button>
                </div>


            </div>
        </form>
    </div>
</div>
