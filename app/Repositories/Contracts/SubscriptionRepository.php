<?php

namespace App\Repositories\Contracts;

/* *
 * Interface SubscriptionRepository
 */

use App\Models\Plan;
use App\Models\Subscription;

interface SubscriptionRepository extends BaseRepository
{

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  Subscription  $subscription
     *
     * @return mixed
     */
    public function renew(Subscription $subscription);


    /**
     * @param  Subscription  $subscription
     *
     * @param  array  $input
     *
     * @return mixed
     */
    public function rejectPending(Subscription $subscription, array $input);


    /**
     * @param  Subscription  $subscription
     *
     * @return mixed
     */
    public function approvePending(Subscription $subscription);

    /**
     * @param  Subscription  $subscription
     *
     * @return mixed
     */
    public function changePlan(Subscription $subscription);

    /**
     * @param Subscription $subscription
     *
     * @return mixed
     */
    public function destroy(Subscription $subscription);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchApprove(array $ids);

    /**
     * @param array $ids
     *
     * @return mixed
     */
    public function batchCancel(array $ids);


    /**
     * @param  Plan  $plan
     * @param  Subscription  $subscription
     * @param  array  $input
     *
     * @return mixed
     */
    public function payPayment(Plan $plan, Subscription $subscription, array $input);

    /**
     * free subscription
     *
     * @param  Plan  $plan
     *
     * @return mixed
     */
    public function freeSubscription(Plan $plan);
}
