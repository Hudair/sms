<?php

namespace App\Repositories\Contracts;

/* *
 * Interface SettingsRepository
 */

interface SettingsRepository extends BaseRepository
{

    /**
     * general setting
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function general(array $input);


    /**
     * system email setting
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function systemEmail(array $input);


    /**
     * authentication settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function authentication(array $input);

    /**
     * notifications settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function notifications(array $input);


    /**
     * localization settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function localization(array $input);


    /**
     * pusher settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function pusherSettings(array $input);

    /**
     * background job settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function backgroundJob(array $input);


    /**
     * license manager settings
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function license(array $input);


    /**
     * upgrade ultimate sms from old one.
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function upgradeApplication( array $input);
}
