<div class="card">
    <div class="card-body py-2 my-25">
        <div class="col-md-6 col-12">
            <div class="form-body">
                <form class="form form-vertical" action="{{ route('admin.plans.update', $plan->uid) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="name" class="form-label required">{{__('locale.labels.name')}}</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $plan->name }}" name="name" required>
                                @error('name')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <label for="description" class="form-label">{{__('locale.labels.description')}}</label>
                                <input type="text" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ $plan->description }}" name="description">
                                @error('name')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label for="price" class="form-label required">{{__('locale.plans.price')}}</label>
                                <input type="text" id="price" class="form-control text-right @error('price') is-invalid @enderror" value="{{ $plan->price }}" name="price" required>
                                @error('price')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <label for="billing_cycle" class="form-label required">{{__('locale.plans.billing_cycle')}}</label>
                                <select class="form-select" id="billing_cycle" name="billing_cycle">
                                    <option value="daily" {{ $plan->billing_cycle == 'daily' ? 'selected': null }}> {{__('locale.labels.daily')}}</option>
                                    <option value="monthly" {{ $plan->billing_cycle == 'monthly' ? 'selected': null }}>  {{__('locale.labels.monthly')}}</option>
                                    <option value="yearly" {{ $plan->billing_cycle == 'yearly' ? 'selected': null }}>  {{__('locale.labels.yearly')}}</option>
                                    <option value="custom" {{ $plan->billing_cycle == 'custom' ? 'selected': null }}>  {{__('locale.labels.custom')}}</option>
                                </select>
                            </div>
                            @error('billing_cycle')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>


                        <div class="col-sm-6 col-12 show-custom">
                            <div class="mb-1">
                                <label for="frequency_amount" class="form-label required">{{__('locale.plans.frequency_amount')}}</label>
                                <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" value="{{ $plan->frequency_amount }}" name="frequency_amount">
                                @error('frequency_amount')
                                <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6 col-12 show-custom">
                            <div class="mb-1">
                                <label for="frequency_unit" class="form-label required">{{__('locale.plans.frequency_unit')}}</label>
                                <select class="form-select" id="frequency_unit" name="frequency_unit">
                                    <option value="day" {{ $plan->frequency_unit == 'day' ? 'selected': null }}> {{__('locale.labels.day')}}</option>
                                    <option value="week" {{ $plan->frequency_unit == 'week' ? 'selected': null }}>  {{__('locale.labels.week')}}</option>
                                    <option value="month" {{ $plan->frequency_unit == 'month' ? 'selected': null }}>  {{__('locale.labels.month')}}</option>
                                    <option value="year" {{ $plan->frequency_unit == 'year' ? 'selected': null }}>  {{__('locale.labels.year')}}</option>
                                </select>
                            </div>
                            @error('frequency_unit')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <label for="currency_id" class="form-label required">{{__('locale.labels.currency')}}</label>
                                <select class="form-select select2" id="currency_id" name="currency_id">
                                    @foreach($currencies as $currency)
                                        <option {{ $plan->currency_id == $currency->id ? 'selected': null }} value="{{$currency->id}}"> {{ $currency->name }}
                                            ({{$currency->code}})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('currency_id')
                            <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <div class="form-check me-3 me-lg-5 mt-1">
                                    <input type="checkbox"
                                           id="tax_billing_required"
                                           class="form-check-input"
                                           value="true"
                                           name="show_in_customer"
                                            {{ $plan->show_in_customer == true ? 'checked': null }}
                                    >
                                    <label for="show_in_customer" class="form-label">{{__('locale.plans.show_in_customer')}}</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <div class="form-check me-3 me-lg-5 mt-1">
                                    <input type="checkbox"
                                           id="tax_billing_required"
                                           class="form-check-input"
                                           value="true"
                                           name="tax_billing_required"
                                            {{ $plan->tax_billing_required == true ? 'checked': null }}
                                    >
                                    <label for="tax_billing_required" class="form-label">{{__('locale.plans.billing_information_required')}}</label>
                                </div>
                                <p><small class="text-muted">{{__('locale.plans.ask_tax_billing_information_subscribing_plan')}}</small></p>

                            </div>
                        </div>


                        <div class="col-12">
                            <div class="mb-1">
                                <div class="form-check me-3 me-lg-5 mt-1">
                                    <input type="checkbox" class="form-check-input" id="is_popular" value="true" name="is_popular" {{ $plan->is_popular == true ? 'checked': null }}>
                                    <label class="form-label" for="is_popular">{{__('locale.labels.is_popular')}}</label>
                                </div>
                                <p><small class="text-muted">{{__('locale.plans.set_this_plan_as_popular')}}</small></p>

                            </div>
                        </div>


                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary mb-1">
                                <i data-feather="save"></i> {{__('locale.buttons.save')}}
                            </button>
                        </div>


                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
