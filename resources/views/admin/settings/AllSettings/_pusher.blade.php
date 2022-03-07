<div class="col-md-6 col-12">
    <div class="form-body">

        <div class="col-12">
            <p>{!! __('locale.description.pusher') !!} {{config('app.name')}}</p>
        </div>

        <form class="form form-vertical" action="{{ route('admin.settings.pusher') }}" method="post">
            @csrf

            <div class="col-12">
                <div class="form-group">
                    <label for="app_id" class="required">APP ID</label>
                    <input type="text" id="app_id" name="app_id" required class="form-control" value="{{ config('broadcasting.connections.pusher.app_id') }}">
                    @error('app_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="app_key" class="required">Key</label>
                    <input type="text" id="app_key" name="app_key" required class="form-control" value="{{ config('broadcasting.connections.pusher.key') }}">
                    @error('app_key')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="app_secret" class="required">Secret</label>
                    <input type="text" id="app_secret" name="app_secret" required class="form-control" value="{{ config('broadcasting.connections.pusher.secret') }}">
                    @error('app_secret')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="app_cluster" class="required">Cluster</label>
                    <input type="text" id="app_cluster" name="app_cluster" required class="form-control" value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
                    @error('app_cluster')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    <i class="feather icon-save"></i> {{__('locale.buttons.save')}}
                </button>
            </div>


        </form>
    </div>
</div>
