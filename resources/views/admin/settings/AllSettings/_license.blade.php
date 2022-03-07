<div class="col-12">
    <div class="divider divider-primary">
        <div class="divider-text"><h4 class="text-primary">Your current license</h4></div>
    </div>
    <p>Thank you for purchasing Ultimate SMS! Below is your license key, also known as Purchase Code. Your license type is <strong class="text-primary"> {{\App\Helpers\Helper::app_config('license_type')}}</strong></p>
    <h4>{{\App\Helpers\Helper::app_config('license')}}</h4>

    <div class="divider divider-primary mt-3">
        <div class="divider-text"><h4 class="text-primary">License types</h4></div>
    </div>
    <p>When you purchase Ultimate SMS from Envato website, you are actually purchasing a license to use the product.
        There are 2 types of license that are issued</p>

    <h4>Regular License</h4>
    <p>All features are available, for a single end product which end users are NOT charged for</p>

    <h4>Extended License</h4>
    <p>All features are available, for a single end product which end users can be charged for (software as a service)</p>

    <div class="divider divider-primary mt-3">
        <div class="divider-text"><h4 class="text-primary">Update your license</h4></div>
    </div>

    <div class="form-body">
        <form class="form form-vertical" action="{{ route('admin.settings.license') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="license" class="required">Insert your purchase code</label>
                <input type="text" class="form-control" name="license" id="license" required>
                <span class="text-primary">Enter the licence key (purchase code) then hit the Update button</span>
                @error('license')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-sm"><i class="feather icon-save"></i> {{ __('locale.buttons.update') }} </button>

        </form>
    </div>

</div>
