<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ThemeCustomizerRequest;
use App\Models\AppConfig;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeCustomizerController extends AdminBaseController
{

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Theme Customizer')],
                ['name' => __('locale.menu.Theme Customizer')],
        ];

        return view('admin.ThemeCustomizer.index', compact('breadcrumbs'));
    }


    /**
     * @param  ThemeCustomizerRequest  $request
     *
     * @return RedirectResponse
     */
    public function postCustomizer(ThemeCustomizerRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.theme.customizer')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input = $request->all();

        if (isset($request->sidebarCollapsed)) {
            $sidebarCollapsed = "true";
        } else {
            $sidebarCollapsed = "false";
        }
        if (isset($request->pageHeader)) {
            $pageHeader = "true";
        } else {
            $pageHeader = "false";
        }
        if ($request->navbarColor == 'custom') {
            $navbarColor = $request->navbarCustomColor;
        } else {
            $navbarColor = $request->navbarColor;
        }


        AppConfig::setEnv('THEME_NAVBAR_COLOR', $navbarColor);

        $customizer_settings = '
        THEME_LAYOUT_TYPE='.$input['mainLayoutType'].'
THEME_SKIN='.$input['theme'].'
THEME_NAVBAR_TYPE='.$input['navbarType'].'
THEME_FOOTER_TYPE='.$input['footerType'].'
THEME_LAYOUT_WIDTH='.$input['layoutWidth'].'
THEME_MENU_COLLAPSED='.$sidebarCollapsed.'
THEME_BREADCRUMBS='.$pageHeader.'
';

        // @ignoreCodingStandard
        $env        = file_get_contents(base_path('.env'));
        $rows       = explode("\n", $env);
        $unwanted   = "THEME_LAYOUT_TYPE|THEME_SKIN|THEME_NAVBAR_TYPE|THEME_FOOTER_TYPE|THEME_LAYOUT_WIDTH|THEME_MENU_COLLAPSED|THEME_BREADCRUMBS";
        $cleanArray = preg_grep("/$unwanted/i", $rows, PREG_GREP_INVERT);

        $cleanString = implode("\n", $cleanArray);
        $env         = $cleanString.$customizer_settings;

        file_put_contents(base_path('.env'), $env);

        return redirect()->route('admin.theme.customizer')->with([
                'status'  => 'success',
                'message' => 'Theme customizer was successfully saved',
        ]);
    }
}
