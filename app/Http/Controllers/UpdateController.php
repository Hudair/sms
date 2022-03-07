<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseManager;
use App\Helpers\EnvironmentManager;
use App\Helpers\FinalInstallManager;
use App\Helpers\Helper;
use App\Helpers\InstalledFileManager;
use App\Helpers\PermissionsChecker;
use App\Helpers\RequirementsChecker;
use App\Models\AppConfig;
use App\Models\Blacklists;
use App\Models\ContactGroups;
use App\Models\Contacts;
use App\Models\Customer;
use App\Models\Keywords;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Templates;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use function PHPUnit\Framework\isReadable;

class UpdateController extends Controller
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


    public function welcome()
    {

        if (config('app.env') == 'demo') {
            return redirect()->back()->with([
                    'status'  => 'error',
                    'message' => 'Sorry!! This feature is not available in demo mode',
            ]);
        }

        if (config('app.version') == '3.0.1') {
            return redirect()->back()->with([
                    'status'  => 'success',
                    'message' => 'You are already in latest version',
            ]);
        }

        if (config('app.version') == '3.0.0') {

            $payment_gateways = [
                    [
                            'name'    => 'aamarPay',
                            'type'    => 'aamarpay',
                            'options' => json_encode([
                                    'store_id'      => 'store_id',
                                    'signature_key' => 'signature_key',
                                    'environment'   => 'sandbox',
                            ]),
                            'status'  => true,
                    ],
                    [
                            'name'    => 'Flutterwave',
                            'type'    => 'flutterwave',
                            'options' => json_encode([
                                    'public_key'  => 'public_key',
                                    'secret_key'  => 'secret_key',
                                    'environment' => 'sandbox',
                            ]),
                            'status'  => true,
                    ],
            ];

            foreach ($payment_gateways as $gateway) {
                PaymentMethods::create($gateway);
            }

            $plans = Plan::cursor();
            foreach ($plans as $plan) {
                $get_options = json_decode($plan->options, true);
                $output      = array_replace($get_options, [
                        'per_unit_price' => 0.3,
                ]);

                $plan->update(['options' => $output]);
            }

            $users = User::where('is_customer', 1)->cursor();

            foreach ($users as $user) {
                $balance = $user->customer->getSendingQuota() - $user->customer->getSendingQuotaUsage();
                $user->update([
                        'sms_unit' => $balance,
                ]);
            }

            AppConfig::setEnv('APP_VERSION', '3.0.1');

            return redirect()->back()->with([
                    'status'  => 'success',
                    'message' => 'You are now in latest version',
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


        return view('Installer.update', compact('requirements', 'phpSupportInfo', 'pageConfigs', 'permissions'));
    }


    public function saveDatabase(Request $request)
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

        AppConfig::setEnv('APP_NAME', $request->app_name);
        AppConfig::setEnv('APP_URL', $request->app_url);
        AppConfig::setEnv('DB_CONNECTION', $request->database_connection);
        AppConfig::setEnv('DB_HOST', $request->database_host);
        AppConfig::setEnv('DB_PORT', $request->database_port);
        AppConfig::setEnv('DB_DATABASE', $request->database_name);
        AppConfig::setEnv('DB_USERNAME', $request->database_username);
        AppConfig::setEnv('DB_PASSWORD', $request->database_password);
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
                                ]),
                        ],
                ],
        ]);

        try {
            DB::connection()->getPdo();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function database(Request $request)
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


        $app_config = DB::table('sys_app_config')->cursor()->toArray();

        $config_data = [
                [
                        'setting' => 'app_keyword',
                        'value'   => 'ultimate sms, codeglen, bulk sms, sms, sms marketing, laravel, framework',
                ],
                [
                        'setting' => 'app_logo',
                        'value'   => 'images/logo/1e4fd743756c6c73940e089cf853b602.png',
                ],
                [
                        'setting' => 'app_favicon',
                        'value'   => 'images/logo/428eedaaee070f72c0a4f14aa08be0c4.png',
                ],
                [
                        'setting' => 'valid_domain',
                        'value'   => 'yes',
                ],
                [
                        'setting' => 'app_stage',
                        'value'   => 'live',
                ],
                [
                        'setting' => 'maintenance_mode',
                        'value'   => false,
                ],
                [
                        'setting' => 'maintenance_mode_message',
                        'value'   => 'We\'re undergoing a bit of scheduled maintenance.',
                ],
                [
                        'setting' => 'maintenance_mode_end',
                        'value'   => 'Jan 5, 2021 15:37:25',
                ],
                [
                        'setting' => 'php_bin_path',
                        'value'   => '/usr/bin/php',
                ],
                [
                        'setting' => 'two_factor',
                        'value'   => false,
                ],
                [
                        'setting' => 'two_factor_send_by',
                        'value'   => 'email',
                ],
                [
                        'setting' => 'captcha_in_login',
                        'value'   => false,
                ],
                [
                        'setting' => 'captcha_in_client_registration',
                        'value'   => false,
                ],
                [
                        'setting' => 'captcha_site_key',
                        'value'   => null,
                ],
                [
                        'setting' => 'captcha_secret_key',
                        'value'   => null,
                ],
                [
                        'setting' => 'login_with_facebook',
                        'value'   => false,
                ],
                [
                        'setting' => 'facebook_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'facebook_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_twitter',
                        'value'   => false,
                ],
                [
                        'setting' => 'twitter_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'twitter_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_google',
                        'value'   => false,
                ],
                [
                        'setting' => 'google_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'google_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'login_with_github',
                        'value'   => false,
                ],
                [
                        'setting' => 'github_client_id',
                        'value'   => '',
                ],
                [
                        'setting' => 'github_client_secret',
                        'value'   => '',
                ],
                [
                        'setting' => 'notification_sms_gateway',
                        'value'   => null,
                ],
                [
                        'setting' => 'notification_sender_id',
                        'value'   => config('app.name'),
                ],
                [
                        'setting' => 'notification_phone',
                        'value'   => null,
                ],
                [
                        'setting' => 'notification_from_name',
                        'value'   => config('app.name'),
                ],
                [
                        'setting' => 'notification_email',
                        'value'   => null,
                ],
                [
                        'setting' => 'sender_id_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'sender_id_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'user_registration_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'user_registration_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'subscription_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'subscription_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'keyword_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'keyword_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'phone_number_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'phone_number_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'block_message_notification_email',
                        'value'   => false,
                ],
                [
                        'setting' => 'block_message_notification_sms',
                        'value'   => false,
                ],
                [
                        'setting' => 'unsubscribe_message',
                        'value'   => 'Reply Stop to unsubscribe',
                ],
                [
                        'setting' => 'custom_script',
                        'value'   => '',
                ],
                [
                        'setting' => 'language',
                        'value'   => '1',
                ],
        ];
        foreach ($app_config as $config) {

            if ($config->setting == 'AppName') {
                $config_data[] = [
                        'setting' => 'app_name',
                        'value'   => $config->value,
                ];
                $config_data[] = [
                        'setting' => 'from_name',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'AppTitle') {
                $config_data[] = [
                        'setting' => 'app_title',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'purchase_key') {
                $config_data[] = [
                        'setting' => 'license',
                        'value'   => $config->value,
                ];
            }
            if ($config->setting == 'license_type') {
                $config_data[] = [
                        'setting' => 'license_type',
                        'value'   => $config->value,
                ];
            }
            if ($config->setting == 'Email') {
                $config_data[] = [
                        'setting' => 'from_email',
                        'value'   => $config->value,
                ];
            }
            if ($config->setting == 'Address') {
                $config_data[] = [
                        'setting' => 'company_address',
                        'value'   => $config->value,
                ];
            }
            if ($config->setting == 'SoftwareVersion') {
                $config_data[] = [
                        'setting' => 'software_version',
                        'value'   => '3.0.1',
                ];
            }
            if ($config->setting == 'FooterTxt') {
                $config_data[] = [
                        'setting' => 'footer_text',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'Country') {
                $config_data[] = [
                        'setting' => 'country',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'Timezone') {
                $config_data[] = [
                        'setting' => 'timezone',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'Currency') {
                $config_data[] = [
                        'setting' => 'timezone',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'Gateway') {
                $config_data[] = [
                        'setting' => 'driver',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'SMTPHostName') {
                $config_data[] = [
                        'setting' => 'host',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'SMTPUserName') {
                $config_data[] = [
                        'setting' => 'username',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'SMTPPassword') {
                $config_data[] = [
                        'setting' => 'password',
                        'value'   => $config->value,
                ];
            }
            if ($config->setting == 'SMTPPort') {
                $config_data[] = [
                        'setting' => 'port',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'SMTPSecure') {
                $config_data[] = [
                        'setting' => 'encryption',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'DateFormat') {
                $config_data[] = [
                        'setting' => 'date_format',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'client_registration') {
                $config_data[] = [
                        'setting' => 'client_registration',
                        'value'   => $config->value,
                ];
            }

            if ($config->setting == 'registration_verification') {
                $config_data[] = [
                        'setting' => 'registration_verification',
                        'value'   => $config->value,
                ];
            }
        }

        $clients = DB::table('sys_clients')->cursor();

        $client_data = [];

        foreach ($clients->chunk(100) as $chunk) {
            foreach ($chunk as $client) {
                $status = false;
                if ($client->status == 'Active') {
                    $status = true;
                }

                $client_data[] = [
                        'first_name'        => $client->fname,
                        'last_name'         => $client->lname,
                        'image'             => null,
                        'email'             => $client->email,
                        'password'          => $client->password,
                        'sms_unit'          => $client->sms_limit,
                        'status'            => $status,
                        'is_admin'          => false,
                        'is_customer'       => true,
                        'active_portal'     => 'customer',
                        'locale'            => app()->getLocale(),
                        'timezone'          => config('app.timezone'),
                        'email_verified_at' => now(),
                        'customer'          => [
                                'company'            => $client->company,
                                'website'            => $client->website,
                                'address'            => $client->address1,
                                'city'               => $client->city,
                                'postcode'           => $client->postcode,
                                'financial_address'  => $client->address1,
                                'financial_city'     => $client->city,
                                'financial_postcode' => $client->postcode,
                                'tax_number'         => null,
                                'state'              => $client->state,
                                'country'            => $client->country,
                                'phone'              => $client->phone,
                                'notifications'      => json_encode([
                                        'login'        => 'no',
                                        'sender_id'    => 'yes',
                                        'keyword'      => 'yes',
                                        'subscription' => 'yes',
                                        'promotion'    => 'yes',
                                        'profile'      => 'yes',
                                ]),
                        ],
                ];
            }
        }


        $blacklist = DB::table('sys_blacklist_contacts')->cursor();

        $blacklist_data = [];

        foreach ($blacklist->chunk(1000) as $chunk) {
            foreach ($chunk as $list) {
                if ($list->user_id != 0) {

                    $blacklist_data[] = [
                            'uid'        => uniqid(),
                            'user_id'    => $list->user_id + 1,
                            'number'     => $list->numbers,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                    ];
                }

            }
        }

        $contact_lists = DB::table('sys_import_phone_number')->cursor();

        $contact_list_data = [];

        foreach ($contact_lists->chunk(500) as $chunk) {
            foreach ($chunk as $list) {
                if ($list->user_id != 0) {
                    $contact_data = DB::table('sys_contact_list')->select('phone_number as phone', 'email_address as email', 'user_name as username', 'company', 'first_name', 'last_name')->where('pid', $list->id)->get()->toArray();

                    $contacts = [];
                    foreach (array_chunk($contact_data, 500) as $chunk) {
                        foreach ($chunk as $data) {
                            $data                = (array) $data;
                            $data['customer_id'] = $list->user_id + 1;
                            $contacts[]          = $data;
                        }
                    }


                    $contact_list_data[] = [
                            'customer_id' => $list->user_id + 1,
                            'name'        => $list->group_name,
                            'created_at'  => Carbon::now(),
                            'updated_at'  => Carbon::now(),
                            'contacts'    => $contacts,
                    ];
                }
            }
        }


        $keywords = DB::table('sys_keywords')->cursor();

        $keyword_data = [];

        foreach ($keywords as $keyword) {
            $keyword_data[] = [
                    'uid'              => uniqid(),
                    'user_id'          => $keyword->user_id + 1,
                    'currency_id'      => 1,
                    'title'            => $keyword->title,
                    'keyword_name'     => $keyword->keyword_name,
                    'sender_id'        => null,
                    'reply_text'       => $keyword->reply_text,
                    'reply_voice'      => $keyword->reply_voice,
                    'reply_mms'        => $keyword->reply_mms,
                    'price'            => $keyword->price,
                    'billing_cycle'    => 'monthly',
                    'frequency_amount' => '1',
                    'frequency_unit'   => 'month',
                    'validity_date'    => $keyword->validity_date,
                    'status'           => $keyword->status,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
            ];
        }

        $sms_template  = DB::table('sys_sms_templates')->cursor();
        $template_data = [];

        foreach ($sms_template as $template) {
            if ($template->cl_id != 0) {

                $status = false;
                if ($template->status == 'active') {
                    $status = true;
                }

                $template_data[] = [
                        'uid'        => uniqid(),
                        'user_id'    => $template->cl_id + 1,
                        'name'       => $template->template_name,
                        'message'    => $template->message,
                        'status'     => $status,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                ];
            }
        }

        AppConfig::setEnv('DB_PREFIX', 'cg_');

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

            AppConfig::truncate();

            foreach ($config_data as $conf) {
                AppConfig::create($conf);
            }

            unset($config_data);

            if (count($client_data)) {
                $user     = new User();
                $customer = new Customer();

                foreach (array_chunk($client_data, 100) as $chunk) {
                    foreach ($chunk as $client) {
                        $customer_data = $client['customer'];
                        unset($client['customer']);

                        $status = $user->create($client);
                        if ($status) {
                            $customer_data['user_id'] = $status->id;
                            $customer->create($customer_data);
                        }
                    }
                }
            }

            unset($client_data);
            unset($status);

            if (count($blacklist_data)) {
                Blacklists::insert($blacklist_data);
            }

            unset($blacklist_data);

            if (count($contact_list_data)) {
                $groups   = new ContactGroups();
                $contacts = new Contacts();

                foreach (array_chunk($contact_list_data, 100) as $chunk) {
                    foreach ($chunk as $list) {

                        $contacts_data = $list['contacts'];
                        unset($list['contacts']);

                        $status = $groups->create($list);

                        if ($status) {
                            $group_id = $status->id;

                            $contacts_input = [];

                            foreach (array_chunk($contacts_data, 500) as $chunk) {
                                foreach ($chunk as $data) {
                                    $data['uid']        = uniqid();
                                    $data['group_id']   = $group_id;
                                    $data['status']     = 'subscribe';
                                    $data['created_at'] = Carbon::now();
                                    $data['updated_at'] = Carbon::now();

                                    $contacts_input[] = $data;
                                }
                            }

                            $contacts->insert($contacts_input);
                        }
                    }
                }
            }

            unset($contact_list_data);

            Keywords::insert($keyword_data);

            unset($keyword_data);

            Templates::insert($template_data);


            (new FinalInstallManager())->runFinal();
            (new InstalledFileManager())->update();

            AppConfig::setEnv('APP_STAGE', 'Live');

            $appNameSetting = "\n".'APP_VERSION=3.0.1'."\n";
            // @ignoreCodingStandard
            $env        = file_get_contents(base_path('.env'));
            $rows       = explode("\n", $env);
            $unwanted   = "APP_VERSION";
            $cleanArray = preg_grep("/$unwanted/i", $rows, PREG_GREP_INVERT);

            $cleanString = implode("\n", $cleanArray);
            $env         = $cleanString.$appNameSetting;

            file_put_contents(base_path('.env'), $env);


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
