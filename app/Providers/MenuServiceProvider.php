<?php

namespace App\Providers;

use App\Helpers\Helper;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $verticalMenuData   = json_decode(json_encode(Helper::menuData()));

        // Share all menuData to all the views
        \View::share('menuData', [$verticalMenuData]);
    }
}
