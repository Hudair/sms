<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/me', 'APIController@me')->name('profile.me');
Route::get('/balance', 'APIController@balance')->name('profile.balance');


/*
|--------------------------------------------------------------------------
| contact module routes
|--------------------------------------------------------------------------
|
|
|
*/


Route::post('contacts/{group_id}/all', 'ContactsController@allContact')->name('contact.all');
Route::post('contacts/{group_id}/search/{uid}', 'ContactsController@searchContact')->name('contact.search');
Route::post('contacts/{group_id}/store', 'ContactsController@storeContact')->name('contact.store');
Route::patch('contacts/{group_id}/update/{uid}', 'ContactsController@updateContact')->name('contact.update');
Route::delete('contacts/{group_id}/delete/{uid}', 'ContactsController@deleteContact')->name('contact.delete');

/*
|--------------------------------------------------------------------------
| contact groups module route
|--------------------------------------------------------------------------
|
|
|
*/
Route::resource('contacts', 'ContactsController', [
        'only' => ['index', 'store', 'update', 'destroy'],
]);
Route::post('contacts/{group_id}/show', 'ContactsController@show')->name('contacts.show');


/*
|--------------------------------------------------------------------------
| send message module including plain, voice, mms, and whatsapp
|--------------------------------------------------------------------------
|
|
|
*/
Route::get('sms', 'CampaignController@viewAllSMS')->name('sms.index');
Route::post('sms/send', 'CampaignController@smsSend')->name('sms.send');
Route::get('sms/{uid}', 'CampaignController@viewSMS')->name('sms.view');
