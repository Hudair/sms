<?php
Route::group(['middleware' => ['web']], function () {
    Route::get('verify-purchase-code', 'kashem\licenseChecker\ProductVerifyController@verifyPurchaseCode')->name('verify.license');
    Route::post('verify-purchase-code', 'kashem\licenseChecker\ProductVerifyController@postVerifyPurchaseCode');
});
