<?php
  use App\Http\Controllers\LanguageController;

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

    if (config('app.stage') == 'new'){
        return redirect('install');
    }

    return redirect('login');
});

// locale Route
Route::get('lang/{locale}',[LanguageController::class,'swap']);
Route::any('languages',[LanguageController::class,'languages'])->name('languages');
