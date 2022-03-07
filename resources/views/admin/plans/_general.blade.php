<div class="col-md-6 col-12">
    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.plans.update', $plan->uid) }}" method="post">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="name" class="required">{{__('locale.labels.name')}}</label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ $plan->name }}" name="name" required>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label for="description">{{__('locale.labels.description')}}</label>
                        <input type="text" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ $plan->description }}" name="description">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label for="price" class="required">{{__('locale.plans.price')}}</label>
                        <input type="text" id="price" class="form-control text-right @error('price') is-invalid @enderror" value="{{ $plan->price }}" name="price" required>
                        @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>


                <div class="col-12">
                    <fieldset class="form-group">
                        <label for="billing_cycle" class="required">{{__('locale.plans.billing_cycle')}}</label>
                        <select class="form-control" id="billing_cycle" name="billing_cycle">
                            <option value="one_time" {{ $plan->billing_cycle == 'one_time' ? 'selected': null }}> {{__('locale.labels.one_time')}}</option>
                            <option value="daily" {{ $plan->billing_cycle == 'daily' ? 'selected': null }}> {{__('locale.labels.daily')}}</option>
                            <option value="monthly" {{ $plan->billing_cycle == 'monthly' ? 'selected': null }}>  {{__('locale.labels.monthly')}}</option>
                            <option value="yearly" {{ $plan->billing_cycle == 'yearly' ? 'selected': null }}>  {{__('locale.labels.yearly')}}</option>
                            <option value="custom" {{ $plan->billing_cycle == 'custom' ? 'selected': null }}>  {{__('locale.labels.custom')}}</option>
                        </select>
                    </fieldset>
                    @error('billing_cycle')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-sm-6 col-12 show-custom">
                    <div class="form-group">
                        <label for="frequency_amount" class="required">{{__('locale.plans.frequency_amount')}}</label>
                        <input type="text" id="frequency_amount" class="form-control text-right @error('frequency_amount') is-invalid @enderror" value="{{ $plan->frequency_amount }}" name="frequency_amount">
                        @error('frequency_amount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6 col-12 show-custom">
                    <fieldset class="form-group">
                        <label for="frequency_unit" class="required">{{__('locale.plans.frequency_unit')}}</label>
                        <select class="form-control" id="frequency_unit" name="frequency_unit">
                            <option value="day" {{ $plan->frequency_unit == 'day' ? 'selected': null }}> {{__('locale.labels.day')}}</option>
                            <option value="week" {{ $plan->frequency_unit == 'week' ? 'selected': null }}>  {{__('locale.labels.week')}}</option>
                            <option value="month" {{ $plan->frequency_unit == 'month' ? 'selected': null }}>  {{__('locale.labels.month')}}</option>
                            <option value="year" {{ $plan->frequency_unit == 'year' ? 'selected': null }}>  {{__('locale.labels.year')}}</option>
                        </select>
                    </fieldset>
                    @error('frequency_unit')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-12">
                    <fieldset class="form-group">
                        <label for="currency_id" class="required">{{__('locale.labels.currency')}}</label>
                        <select class="form-control select2" id="currency_id" name="currency_id">
                            @foreach($currencies as $currency)
                                <option {{ $plan->currency_id == $currency->id ? 'selected': null }} value="{{$currency->id}}"> {{ $currency->name }}
                                    ({{$currency->code}})
                                </option>
                            @endforeach
                        </select>
                    </fieldset>
                    @error('currency_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="col-12">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="true" name="tax_billing_required" {{ $plan->tax_billing_required == true ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.plans.billing_information_required')}}</span>
                        </div>
                        <p>
                            <small class="text-muted">{{__('locale.plans.ask_tax_billing_information_subscribing_plan')}}</small>
                        </p>

                    </fieldset>
                </div>


                <div class="col-12">
                    <fieldset>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="true" name="is_popular" {{ $plan->is_popular == true ? 'checked': null }}>
                            <span class="vs-checkbox">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                            <span class="">{{__('locale.labels.is_popular')}}</span>
                        </div>
                        <p>
                            <small class="text-muted">{{__('locale.plans.set_this_plan_as_popular')}}</small>
                        </p>

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
