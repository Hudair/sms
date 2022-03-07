<?php

Route::group(
        ['namespace' => 'Auth'],
        function () {
            if (config('account.can_register')) {
                // Registration Routes...
                Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
                Route::post('register', 'RegisterController@register');

                Route::get('/email/verify', 'VerificationController@verificationNotice')->middleware(['auth'])->name('verification.notice');
                Route::get('/email/verify/{id}/{hash}', 'VerificationController@verificationVerify')->middleware(['auth', 'signed'])->name('verification.verify');
                Route::post('/email/verification-notification', 'VerificationController@verificationSend')->middleware(['auth', 'throttle:6,1'])->name('verification.send');
            }

            // Authentication Routes...
            Route::get('login', 'LoginController@showLoginForm')->name('login');
            Route::post('login', 'LoginController@login');
            Route::post('logout', 'LoginController@logout')->name('logout');
            Route::get('avatar/{user}', 'LoginController@avatar')->name('user.avatar');

            Route::get('login/{provider}', 'LoginController@redirectToProvider')->name('social.login');
            Route::get('login/{provider}/callback', 'LoginController@handleProviderCallback')->name('social.callback');

            // Password Reset Routes...
            Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
            Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');

            //two step verification routes
            Route::get('verify/resend', 'TwoFactorController@resend')->name('verify.resend');
            Route::get('verify/backup-code', 'TwoFactorController@backUpCode')->name('verify.backup');
            Route::post('verify/backup-code', 'TwoFactorController@updateBackUpCode');
            Route::resource('verify', 'TwoFactorController')->only(['index', 'store']);

            //common or public data access routes
            Route::get('download-sample-file', 'LoginController@downloadSampleFile')->name('sample.file');

            //test or debug or var_dump route
            Route::get('debug', 'LoginController@debug')->name('debug');

        }
);

Route::group(
        [
                'namespace'  => 'User',
                'as'         => 'user.',
                'middleware' => ['auth', 'verified'],
        ],
        function () {
            /*
             * User Dashboard Specific
             */
            Route::get('/dashboard', 'UserController@index')->name('home');


            /*
             * switch view
             */
            Route::get('/switch-view', 'AccountController@switchView')->name('switch_view');


            /*
             * User Account Specific
             */
            Route::get('account', 'AccountController@index')->name('account');
            Route::get('avatar', 'AccountController@avatar')->name('avatar');
            Route::post('avatar', 'AccountController@updateAvatar');
            Route::post('remove-avatar', 'AccountController@removeAvatar')->name('remove_avatar');


            /*
             * User Profile Update
             */
            Route::patch('account/update', 'AccountController@update')->name('account.update');
            Route::post('account/update-information', 'AccountController@updateInformation')->name('account.update_information');

            Route::post('account/change-password', 'AccountController@changePassword')->name('account.change.password');

            Route::get('account/two-factor/{status}', 'AccountController@twoFactorAuthentication')->name('account.twofactor.auth');
            Route::get('account/generate-two-factor-code', 'AccountController@generateTwoFactorAuthenticationCode')->name('account.twofactor.generate_code');
            Route::post('account/two-factor/{status}', 'AccountController@updateTwoFactorAuthentication');


            Route::get('account/top-up', 'AccountController@topUp')->name('account.top_up');
            Route::post('account/top-up', 'AccountController@checkoutTopUp');
            Route::post('account/pay-top-up', 'AccountController@payTopUp')->name('account.pay');

            //notifications

            Route::post('account/notifications', 'AccountController@notifications')->name('account.notifications');
            Route::post('account/notifications/{notification}/active', 'AccountController@notificationToggle')->name('account.notifications.toggle');
            Route::post('account/notifications/{notification}/delete', 'AccountController@deleteNotification')->name('account.notifications.delete');
            Route::post('notifications/batch_action', 'AccountController@notificationBatchAction')->name('account.notifications.batch_action');

            if (config('account.can_delete')) {
                /*
                 * Account delete
                 */
                Route::delete('account/delete', 'AccountController@delete')->name('account.delete');
            }
        }
);
