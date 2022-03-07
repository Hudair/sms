<?php

$valid = true;
if (phpversion() < "7.4") {
    echo "ERROR: PHP 7.4 or higher is required.<br />";
    exit(0);
}

if (!extension_loaded('mysqli')) {
    echo "ERROR: The requested PHP mysqli extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('pdo')) {
    echo "ERROR: The requested PHP pdo extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('curl')) {
    echo "ERROR: The requested PHP curl extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('openssl')) {
    echo "ERROR: The requested PHP openssl extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('iconv') && !function_exists('iconv')) {
    echo "ERROR: The requested PHP iconv extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('mbstring')) {
    echo "ERROR: The requested PHP Mbstring extension is missing from your system.<br />";
    exit(0);
}


if (!extension_loaded('gd')) {
    echo "ERROR: The requested PHP gd extension is missing from your system.<br />";
    exit(0);
}

if (!extension_loaded('zip')) {
    echo "ERROR: The requested PHP zip extension is missing from your system.<br />";
    exit(0);
}

$url_f_open = ini_get('allow_url_fopen');
if ($url_f_open != "1" && $url_f_open != 'On') {
    echo "ERROR: Please enable the <strong>allow_url_fopen</strong> setting to continue.<br />";
    exit(0);
}

if (!empty(ini_get('open_basedir'))) {
    echo "ERROR: Please disable the <strong>open_basedir</strong> setting to continue.<br />";
    exit(0);
}

if (!function_exists('proc_open')) {
    echo "ERROR: Please enable <strong>proc_open</strong> php function setting to continue.<br />";
    exit(0);
}

if (!function_exists('curl_version')) {
    echo "ERROR: Please enable <strong>Curl</strong> php function setting to continue.<br />";
    exit(0);
}

if (!function_exists('base64_decode')) {
    echo "ERROR: Please enable <strong>base64_decode</strong> php function setting to continue.<br />";
    exit(0);
}


if ($valid == false) {
	exit();
}

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/


 $app->bind('path.public', function() {
    return __DIR__;
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try{

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);   
}catch (Exception $ex){
    dd($ex->getMessage());
}

$response->send();

$kernel->terminate($request, $response);
