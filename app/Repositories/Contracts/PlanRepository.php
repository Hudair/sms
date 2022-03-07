<?php

namespace App\Repositories\Contracts;

/* *
 * Interface PlanRepository
 */

use App\Models\Plan;

interface PlanRepository extends BaseRepository
{

    /**
     * @param  array  $input
     * @param  array  $options
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function store(array $input, array $options, array $billingCycle);

    /**
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function update(Plan $plan, array $input, array $billingCycle);

    /**
     * @param Plan $plan
     *
     * @return mixed
     */

    public function destroy(Plan $plan);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchActive(array $ids);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchDisable(array $ids);


    /**
     * set primary sending server by uid
     *
     * @param  Plan  $plan
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function setPrimarySendingServer(Plan $plan, array $input);

    /**
     * remove sending server by uid
     *
     * @param  Plan  $plan
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function removeSendingServerByUid(Plan $plan, array $input);


    /**
     * update speed limit
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return mixed
     */

    public function updateSpeedLimits(Plan $plan, array $input);


    /**
     * update cutting system
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return mixed
     */

    public function updateCuttingSystem(Plan $plan, array $input);


    /**
     * update sms pricing
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return mixed
     */

    public function updatePricing(Plan $plan, array $input);

    /**
     * copy existing plan
     *
     * @param  Plan  $plan
     * @param  array  $input
     *
     * @return mixed
     */

    public function copy(Plan $plan, array $input);

}
