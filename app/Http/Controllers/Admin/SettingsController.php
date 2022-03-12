<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Requests\LicenseRequest;
use App\Http\Requests\Settings\AuthenticationRequest;
use App\Http\Requests\Settings\NotificationsRequest;
use App\Http\Requests\Settings\PostGeneralRequest;
use App\Http\Requests\Settings\PusherRequest;
use App\Http\Requests\Settings\SystemEmailRequest;
use App\Http\Requests\Settings\UpdateVersionRequest;
use App\Library\Unzipper;
use App\Models\AppConfig;
use App\Models\Language;
use App\Models\SendingServer;
use App\Models\User;
use App\Repositories\Contracts\SettingsRepository;
use Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
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
            User::where('id', 1)->update([
                    'timezone' => $request->timezone,
            ]);
        }

        if ($request->language != config('app.locale')) {
            session(['locale' => $request->language]);
            AppConfig::setEnv('APP_LOCALE', $request->language);
        }

        if ($request->date_format != config('app.date_format')) {
            AppConfig::setEnv('APP_DATE_FORMAT', $request->date_format);
        }

        if ($request->app_keyword != config('app.app_keyword')) {
            AppConfig::setEnv('APP_KEYWORD', $request->app_keyword);
        }

        if ($request->footer_text != config('app.footer_text')) {
            AppConfig::setEnv('APP_FOOTER_TEXT', $request->footer_text);
        }

        $this->settings->general($request->except('_token', 'app_logo', 'app_favicon'));

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'general'])->with([
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

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'system_email'])->with([
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

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'authentication'])->with([
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

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'notifications'])->with([
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

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'pusher'])->with([
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

                return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
                        'status'  => 'success',
                        'message' => 'License updated successfully',
                ]);

            }

            return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
                    'status'  => 'error',
                    'message' => 'Invalid license key',
            ]);
        }

        return redirect()->route('admin.settings.general')->withInput(['tab' => 'license'])->with([
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

    /**
     * @return RedirectResponse
     */
    public function checkAvailableUpdate(): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.update_application')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $app_version      = config('app.version');
        $get_verification = 'https://support.codeglen.com/version/';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_verification);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($app_version == $data) {
            return redirect()->route('admin.settings.update_application')->with([
                    'status'  => 'success',
                    'message' => 'You are using latest version',
            ]);
        }

        return redirect()->route('admin.settings.update_application')->with([
                'update_required' => true,
                'version'         => $data,
        ]);

    }


    public function postUpdateApplication(UpdateVersionRequest $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.settings.update_application')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $purchase_code = $request->input('purchase_code');
        $domain_name   = config('app.url');

        $input = trim($domain_name, '/');
        if ( ! preg_match('#^http(s)?://#', $input)) {
            $input = 'http://'.$input;
        }

        $urlParts    = parse_url($input);
        $domain_name = preg_replace('/^www\./', '', $urlParts['host']);


        $get_verification = 'https://support.codeglen.com/forum/api/get-product-data/'.$purchase_code.'/'.$domain_name;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_verification);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);

        $get_data = json_decode($data, true);

        if (is_array($get_data) && array_key_exists('status', $get_data)) {
            if ($get_data['status'] == 'success') {
                $get_response = Unzipper::extractZipArchive($request->file('update_file'), base_path());

                if (isset($get_response->getData()->status)) {

                    if ($get_response->getData()->status == 'success') {
                        try {
                            Artisan::call('migrate', ['--force' => true]);

                            AppConfig::setEnv('APP_VERSION', $request->version);

                            Auth::logout();

                            return response()->json([
                                    'status'      => 'success',
                                    'redirectURL' => route('login'),
                                    'message'     => 'You have successfully updated your application.',
                            ]);
                        } catch (Exception $e) {

                            return response()->json([
                                    'status'  => 'error',
                                    'message' => $e->getMessage(),
                            ]);

                        }
                    }

                    return response()->json([
                            'message' => $get_response->getData()->message,
                            'status'  => 'error',
                    ]);

                }

                return response()->json([
                        'message' => __('locale.exceptions.something_went_wrong'),
                        'status'  => 'error',
                ]);
            }

            return response()->json([
                    'message' => $get_data['msg'],
                    'status'  => 'error',
            ]);
        }

        return response()->json([
                'message' => 'Invalid request',
                'status'  => 'error',
        ]);
    }
}
