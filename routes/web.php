<?php

use App\Http\Controllers\LanguageController;
use App\Models\Campaigns;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    if (config('app.stage') == 'new') {
        return redirect('install');
    }

    if (config('app.stage') == 'Live' && config('app.version') == '2.8') {
        return redirect('update');
    }

    return redirect('login');
});

// locale Route
Route::get('lang/{locale}', [LanguageController::class, 'swap']);
Route::any('languages', [LanguageController::class, 'languages'])->name('languages');

Route::get('test', function (){

    $campaign = Campaigns::find(6);
    if ($campaign) {
        $campaign->singleProcess();
    }
});
