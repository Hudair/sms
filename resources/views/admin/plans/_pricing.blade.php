<div class="row">
    <div class="col-12">
        <div class="mb-1 mt-1">

            @can('general settings')
                <div class="btn-group">
                    <a href="{{route('admin.plans.settings.coverage', $plan->uid)}}" class="btn btn-primary waves-light waves-effect fw-bold mx-1"> {{__('locale.buttons.add_coverage')}} <i data-feather="plus-circle"></i></a>
                </div>
            @endcan
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body py-2 my-25">
        <div class="col-12">
            <div class="form-body">
                <form class="form form-vertical" action="{{ route('admin.plans.settings.pricing', $plan->uid) }}" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <p>{!! __('locale.description.per_unit_price')!!}</p>
                            <div class="mb-1">
                                <label for="per_unit_price" class="form-label required">{{__('locale.labels.per_unit_price')}}</label>
                                <div class="input-group input-group-merge mb-2">
                                    <input type="text"
                                           id="per_unit_price"
                                           class="form-control @error('per_unit_price') is-invalid @enderror"
                                           value="{{ $options['per_unit_price'] }}" name="per_unit_price"
                                           required>

                                    <span class="input-group-text">{{ str_replace('{PRICE}', '', $plan->currency->format) }}</span>
                                    @error('per_unit_price')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-1">
                            <button type="submit" class="btn btn-primary mb-1"><i data-feather="save"></i> {{__('locale.buttons.save')}}</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Basic table -->
<section id="datatables-basic">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <table class="table datatables-basic">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ __('locale.labels.id') }}</th>
                        <th>{{__('locale.labels.name')}} </th>
                        <th>{{__('locale.labels.iso_code')}}</th>
                        <th>{{__('locale.labels.country_code')}}</th>
                        <th>{{__('locale.labels.status')}}</th>
                        <th>{{__('locale.labels.actions')}}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
<!--/ Basic table -->
