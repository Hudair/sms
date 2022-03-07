<?php

namespace App\Repositories\Contracts;

/* *
 * Interface SenderIDRepository
 */

use App\Models\Senderid;
use App\Models\SenderidPlan;

interface SenderIDRepository extends BaseRepository
{

    /**
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function store(array $input, array $billingCycle);

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function storeCustom(array $input);

    /**
     * @param  Senderid  $senderid
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function update(Senderid $senderid, array $input, array $billingCycle);

    /**
     * @param  Senderid  $senderid
     * @param  int|null  $user_id
     *
     * @return mixed
     */

    public function destroy(Senderid $senderid, int $user_id = null);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);

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
    public function batchBlock(array $ids);

    /**
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function storePlan(array $input, array $billingCycle);

    /**
     * pay sender id payment
     *
     * @param  Senderid  $senderid
     * @param  array  $input
     *
     * @return mixed
     */
    public function payPayment(Senderid $senderid, array $input);

}
