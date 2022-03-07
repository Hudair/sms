<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseManager;
use App\Helpers\EnvironmentManager;
use App\Helpers\FinalInstallManager;
use App\Helpers\InstalledFileManager;
use App\Helpers\PermissionsChecker;
use App\Helpers\RequirementsChecker;
use App\Models\AppConfig;
use App\Models\Customer;
use App\Models\User;
use DB;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Validator;

class InstallerController extends Controller
{

    /**
     * @var RequirementsChecker
     */
    protected $requirements;
    protected $EnvironmentManager;

    private $databaseManager;

    /**
     * @param  RequirementsChecker  $checker
     * @param  EnvironmentManager  $environmentManager
     * @param  DatabaseManager  $databaseManager
     */
    public function __construct(RequirementsChecker $checker, EnvironmentManager $environmentManager, DatabaseManager $databaseManager)
    {
        $this->requirements       = $checker;
        $this->EnvironmentManager = $environmentManager;
        $this->databaseManager    = $databaseManager;
    }

    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function welcome()
    {

        if (config('app.env') == 'demo') {
            return redirect()->back()->with([
                    'status'  => 'error',
                    'message' => 'Sorry!! This feature is not available in demo mode',
            ]);
        }

        $phpSupportInfo = $this->requirements->checkPHPversion(
                config('installer.core.minPhpVersion')
        );
        $requirements   = $this->requirements->check(
                config('installer.requirements')
        );

        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        $getPermissions = new PermissionsChecker();

        $permissions = $getPermissions->check(
                config('installer.permissions')
        );


        return view('Installer.welcome', compact('requirements', 'phpSupportInfo', 'pageConfigs', 'permissions'));
    }


    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function saveDatabase(Request $request): JsonResponse
    {

        $rules = config('installer.environment.form.rules');

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors(),
            ]);
        }

        if ( ! $this->checkDatabaseConnection($request)) {
            return response()->json([
                    'status'  => 'error',
                    'message' => [
                            'database_connection' => [
                                    'Could not connect to the database.',
                            ],
                    ],
            ]);
        }

        $app_url = rtrim($request->app_url, '/');

        AppConfig::setEnv('APP_NAME', $request->app_name);
        AppConfig::setEnv('APP_URL', $app_url);
        AppConfig::setEnv('DB_CONNECTION', $request->database_connection);
        AppConfig::setEnv('DB_HOST', $request->database_host);
        AppConfig::setEnv('DB_PORT', $request->database_port);
        AppConfig::setEnv('DB_DATABASE', $request->database_name);
        AppConfig::setEnv('DB_USERNAME', $request->database_username);
        AppConfig::setEnv('DB_PASSWORD', $request->database_password);
        AppConfig::setEnv('DB_PREFIX', $request->database_prefix);
        AppConfig::setEnv('URL_FORCE_HTTPS', $request->https_enable);

        return response()->json([
                'status'  => 'success',
                'message' => 'Settings update successfully',
        ]);

    }


    /**
     * @param  Request  $request
     *
     * @return bool
     */
    private function checkDatabaseConnection(Request $request): bool
    {
        $connection = $request->input('database_connection');

        $settings = config("database.connections.$connection");

        if ($request->input('database_prefix')) {
            $prefix = $request->input('database_prefix');
        } else {
            $prefix = 'cg_';
        }

        config([
                'database' => [
                        'default'     => $connection,
                        'connections' => [
                                $connection => array_merge($settings, [
                                        'driver'   => $connection,
                                        'host'     => $request->input('database_host'),
                                        'port'     => $request->input('database_port'),
                                        'database' => $request->input('database_name'),
                                        'username' => $request->input('database_username'),
                                        'password' => $request->input('database_password'),
                                        'prefix'   => $prefix,
                                ]),
                        ],
                ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function database(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name'  => 'nullable|string|max:50',
                'email'      => 'required|email',
                'password'   => 'required|min:8',
                'timezone'   => 'required|string',
                'admin_path' => 'required|string',
                'customer'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors(),
            ]);
        }

        $response = $this->databaseManager->migrateAndSeed();

        if (isset($response['status'])) {

            $user = User::find(1);
            if ($user) {
                $user->first_name    = $request->first_name;
                $user->last_name     = $request->last_name;
                $user->email         = $request->email;
                $user->password      = Hash::make($request->password);
                $user->timezone      = $request->timezone;
                $user->timezone      = $request->timezone;
                $user->active_portal = 'admin';


                AppConfig::setEnv('APP_TIMEZONE', $request->timezone);
                AppConfig::setEnv('ADMIN_PATH', $request->admin_path);

                if ($request->customer == 1) {

                    $user->is_customer = true;

                    Customer::create([
                            'user_id'       => $user->id,
                            'company'       => null,
                            'website'       => config('app.url'),
                            'notifications' => json_encode([
                                    'login'        => 'no',
                                    'sender_id'    => 'yes',
                                    'keyword'      => 'yes',
                                    'subscription' => 'yes',
                                    'promotion'    => 'yes',
                                    'profile'      => 'yes',
                            ]),
                    ]);
                }
                $user->save();
            }


            (new FinalInstallManager())->runFinal();
            (new InstalledFileManager())->update();

            AppConfig::setEnv('APP_STAGE', 'Live');
            AppConfig::setEnv('APP_VERSION', '3.0.1');

            return response()->json([
                    'status'       => 'success',
                    'response_url' => route('login'),
                    'message'      => $response['message'],
            ]);

        }

        return response()->json([
                'status'  => 'error',
                'message' => [
                        'exception' => [
                                __('locale.exceptions.something_went_wrong'),
                        ],
                ],
        ]);

    }

}
