<?php

namespace App\Repositories\Contracts;

/* *
 * Interface SendingServerRepository
 */

use App\Models\SendingServer;

interface SendingServerRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  SendingServer  $sendingServer
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(SendingServer $sendingServer, array $input);

    /**
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function storeCustom(array $input);


    /**
     * @param  SendingServer  $sendingServer
     * @param  array  $input
     *
     * @return mixed
     */
    public function updateCustom(SendingServer $sendingServer, array $input);


    /**
     * @param  SendingServer  $sendingServer
     * @param  int|null  $user_id
     *
     * @return mixed
     */

    public function destroy(SendingServer $sendingServer, int $user_id = null);

    /**
     * @param  array  $ids
     * @param  int|null  $user_id
     *
     * @return mixed
     */
    public function batchDestroy(array $ids , int $user_id = null);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchActive(array $ids);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDisable(array $ids);


    /**
     * @param  SendingServer  $sendingServer
     * @param  array  $input
     *
     * @return mixed
     *
     */
    public function sendTestSMS(SendingServer $sendingServer, array $input);

    /**
     * All Sending Server
     */
    public function allSendingServer();

}
