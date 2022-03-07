<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PluginsController extends Controller
{
    public function plugins()
    {
        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['name' => __('locale.menu.Plugins')],
        ];


        return view('admin.Plugins.index', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
