@if(! \App\Helpers\Helper::exec_enabled())
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="alert-body">
            {{$get_message}}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="col-12">
    <p> {!! __('locale.description.background_jobs') !!} </p>
</div>

<div class="col-12">
    <div class="form-body">

        @foreach($paths as $p)
            <div class="mb-1">
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" name="php_bin_path" value="{{$p}}" @if($p == $server_php_path) checked @endif>
                    <label class="form-label">{{$p}}</label>
                </div>
            </div>
        @endforeach

    </div>

    <div class="divider divider-primary">
        <div class="divider-text">{{ __('locale.labels.background_job') }}</div>
    </div>

    <div class="col-12">
        <p class="text-bold-600">Insert the following line to your system's contab.Please note, below timings for running the cron jobs are the recommended, you can change it if you want.</p>
    </div>

    <pre class="language-php">
        <code class="language-php"> * * * * * <span class="current_path_value">{!! $server_php_path !!}</span> -d register_argc_argv=On {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1</code>
    </pre>
</div>
