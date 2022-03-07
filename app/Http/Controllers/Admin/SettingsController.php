<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Requests\LicenseRequest;
use App\Http\Requests\Settings\AuthenticationRequest;
use App\Http\Requests\Settings\NotificationsRequest;
use App\Http\Requests\Settings\PostGeneralRequest;
use App\Http\Requests\Settings\PusherRequest;
use App\Http\Requests\Settings\SystemEmailRequest;
use App\Models\AppConfig;
use App\Models\Language;
use App\Models\SendingServer;
use App\Repositories\Contracts\SettingsRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends AdminBaseController
{
    protected $settings;

    /**
     * SettingsController constructor.
     *
     * @param  SettingsRepository  $settings
     */
    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Update all system settings.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function general()
    {

        $this->authorize('general settings');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.All Settings')],
        ];

        $language        = Language::where('status', true)->get();
        $sending_servers = SendingServer::where('status', true)->get();


        // Suggestion paths
        $paths = [
                '/usr/bin/php',
                '/usr/local/bin/php',
                '/bin/php',
                '/usr/bin/php7',
                '/usr/bin/php7.0',
                '/usr/bin/php70',
                '/usr/bin/php7.1',
                '/usr/bin/php71',
                '/usr/bin/php56',
                '/usr/bin/php5.6',
                '/opt/plesk/php/5.6/bin/php',
                '/opt/plesk/php/7.0/bin/php',
                '/opt/plesk/php/7.1/bin/php',
        ];

        // try to detect system's PHP CLI
        if (Helper::exec_enabled()) {
            try {
                $paths           = array_unique(array_merge($paths, explode(" ", exec("whereis php"))));
                $server_php_path = exec('which php');
                if ($server_php_path == "") {
                    $server_php_path = Helper::app_config('php_bin_path');
                }
                $get_message = '';
            } catch (Exception $e) {
                $server_php_path = Helper::app_config('php_bin_path');
                $get_message     = $e->getMessage();
            }
        } else {
            $server_php_path = Helper::app_config('php_bin_path');
            $get_message     = 'WARNING: Please enable PHP `exec` function to validate the cron job setting';
        }

        $paths = array_values(array_filter($paths, function ($path) {
            try {
                return is_executable($path) && preg_match($path, "/php[0-9\.a-z]{0,3}$/i");
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }));

        return view('admin.settings.AllSettings.system_settings', compact('breadcrumbs', 'language', 'sending_servers', 'paths', 'get_message', 'server_php_path'));

    }


    /**
     * update general settings
     *
     * @param  PostGeneralRequest  $request
     *
     * @return RedirectResponse
     */

    public function postGeneral(PostGeneralRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        if (isset($request->app_logo) && $request->hasFile('app_logo') && $request->file('app_logo')->isValid()) {
            AppConfig::uploadFile($request->file('app_logo'), 'app_logo');
        }

        if (isset($request->app_favicon) && $request->hasFile('app_favicon') && $request->file('app_favicon')->isValid()) {
            AppConfig::uploadFile($request->file('app_favicon'), 'app_favicon');
        }

        if ($request->app_name != config('app.name')) {
            AppConfig::setEnv('APP_NAME', $request->app_name);
        }

        if ($request->app_title != config('app.title')) {
            AppConfig::setEnv('APP_TITLE', $request->app_title);
        }

        if ($request->country != config('app.country')) {
            AppConfig::setEnv('APP_COUNTRY', $request->country);
        }

        if ($request->timezone != config('app.timezone')) {
            AppConfig::setEnv('APP_TIMEZONE', $request->timezone);
        }

        if ($request->language != config('app.locale')) {
            session(['locale' => $request->language]);
            AppConfig::setEnv('APP_LOCALE', $request->language);
        }

        if ($request->app_keyword != config('app.app_keyword')) {
            AppConfig::setEnv('APP_KEYWORD', $request->app_keyword);
        }

        if ($request->footer_text != config('app.footer_text')) {
            AppConfig::setEnv('APP_FOOTER_TEXT', $request->footer_text);
        }

        $this->settings->general($request->except('_token', 'app_logo', 'app_favicon'));

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }


    /**
     * update system email settings
     *
     * @param  SystemEmailRequest  $request
     *
     * @return RedirectResponse
     */
    public function email(SystemEmailRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->systemEmail($request->except('_token'));

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    /**
     * update authentication settings
     *
     * @param  AuthenticationRequest  $request
     *
     * @return RedirectResponse
     */
    public function authentication(AuthenticationRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->authentication($request->except('_token'));

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }


    /**
     * update notifications settings
     *
     * @param  NotificationsRequest  $request
     *
     * @return RedirectResponse
     */
    public function notifications(NotificationsRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->settings->notifications($request->except('_token'));

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
        ]);
    }

    /**
     * update pusher settings
     *
     * @param  PusherRequest  $request
     *
     * @return RedirectResponse
     */
    public function pusher(PusherRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->settings->pusherSettings($request->except('_token'));

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'success',
                'message' => __('locale.settings.settings_successfully_updated'),
        ]);

    }

    /**
     * @param  LicenseRequest  $request
     *
     * @return RedirectResponse
     */
    public function license(LicenseRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $purchase_code    = $request->input('license');
        $get_data = array();
		$get_data['status'] = 'success';
		$get_data['license_type'] = 'Extended license';
		
		

        if (is_array($get_data) && array_key_exists('status', $get_data)) {
            if ($get_data['status'] == 'success') {
                AppConfig::where('setting', 'license')->update(['value' => $purchase_code]);
                AppConfig::where('setting', 'license_type')->update(['value' => $get_data['license_type']]);
                AppConfig::where('setting', 'valid_domain')->update(['value' => 'yes']);

                return redirect()->route('admin.settings.general')->with([
                        'status'  => 'success',
                        'message' => 'License updated successfully',
                ]);

            }

            return redirect()->route('admin.settings.general')->with([
                    'status'  => 'error',
                    'message' => 'Invalid license key',
            ]);
        }

        return redirect()->route('admin.settings.general')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

    /**
     * manage maintenance mode
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function maintenanceMode()
    {

        $this->authorize('manage maintenance_mode');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.All Settings')],
        ];


        return view('admin.settings.system_settings', compact('breadcrumbs'));
    }

    public function updateApplication()
    {
        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.All Settings')],
        ];


        return view('admin.settings.UpdateApplication.index', compact('breadcrumbs'));

    }

}
