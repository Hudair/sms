<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
        'core'                   => [
                'minPhpVersion' => '7.4.0',
        ],
        'final'                  => [
                'key'     => true,
                'publish' => false,
        ],
        'requirements'           => [
                'php'    => [
                        'openssl',
                        'pdo',
                        'bcmath',
                        'ctype',
                        'fileinfo',
                        'mbstring',
                        'tokenizer',
                        'xml',
                        'JSON',
                        'cURL',
                ],
                'apache' => [
                        'mod_rewrite',
                ],
        ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list bellow.
    |
    */
        'permissions'            => [
                'storage/framework/' => '775',
                'storage/logs/'      => '775',
                'bootstrap/cache/'   => '775',
        ],

    /*
    |--------------------------------------------------------------------------
    | Environment Form Wizard Validation Rules & Messages
    |--------------------------------------------------------------------------
    |
    | This are the default form field validation rules. Available Rules:
    | https://laravel.com/docs/5.4/validation#available-validation-rules
    |
    */
        'environment'            => [
                'form' => [
                        'rules' => [
                                'app_name'            => 'required|string|max:50',
                                'app_url'             => 'required|url',
                                'database_connection' => 'required|string|max:50',
                                'database_host'       => 'required|string|max:50',
                                'database_port'       => 'required|numeric',
                                'database_name'       => 'required|string|max:50',
                                'database_username'   => 'required|string|max:50',
                                'database_password'   => 'nullable|string|max:50',
                                'https_enable'        => 'required|string',
                        ],
                ],
        ],

    /*
    |--------------------------------------------------------------------------
    | Installed Middleware Options
    |--------------------------------------------------------------------------
    | Different available status switch configuration for the
    | canInstall middleware located in `canInstall.php`.
    |
    */
        'installed'              => [
                'redirectOptions' => [
                        'route' => [
                                'name' => 'welcome',
                                'data' => [],
                        ],
                        'abort' => [
                                'type' => '404',
                        ],
                        'dump'  => [
                                'data' => 'Dumping a not found message.',
                        ],
                ],
        ],

    /*
    |--------------------------------------------------------------------------
    | Selected Installed Middleware Option
    |--------------------------------------------------------------------------
    | The selected option fo what happens when an installer instance has been
    | Default output is to `/resources/views/error/404.blade.php` if none.
    | The available middleware options include:
    | route, abort, dump, 404, default, ''
    |
    */
        'installedAlreadyAction' => '',

    /*
    |--------------------------------------------------------------------------
    | Updater Enabled
    |--------------------------------------------------------------------------
    | Can the application run the '/update' route with the migrations.
    | The default option is set to False if none is present.
    | Boolean value
    |
    */
        'updaterEnabled'         => 'true',

];
