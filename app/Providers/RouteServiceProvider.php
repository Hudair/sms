<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {

        Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

        Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/public.php'));

        Route::middleware(['web', 'auth', 'can:access backend', 'ValidProduct', 'twofactor'])
                ->namespace($this->namespace.'\Admin')
                ->prefix(config('app.admin_path'))
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

        Route::middleware(['web', 'twofactor'])
                ->namespace($this->namespace)
                ->group(base_path('routes/auth.php'));

        Route::middleware(['web', 'auth', 'can:access_backend', 'ValidProduct', 'twofactor'])
                ->namespace($this->namespace.'\Customer')
                ->as('customer.')
                ->group(base_path('routes/customer.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $this->configureRateLimiting();

        Route::prefix('api/v3')
                ->name('api.')
                ->middleware(['api', 'auth:sanctum', 'json.response'])
                ->namespace($this->namespace.'\API')
                ->group(base_path('routes/api.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('app.api_rate_limit'))->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
