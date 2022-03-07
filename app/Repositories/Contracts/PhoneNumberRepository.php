<?php

namespace App\Repositories\Contracts;

/* *
 * Interface PhoneNumberRepository
 */

use App\Models\PhoneNumbers;

interface PhoneNumberRepository extends BaseRepository
{

    /**
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function store(array $input, array $billingCycle);

    /**
     * @param  PhoneNumbers  $number
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function update(PhoneNumbers $number, array $input, array $billingCycle);

    /**
     * @param  PhoneNumbers  $number
     *
     * @return mixed
     */

    public function destroy(PhoneNumbers $number);

    /**
     * @param  PhoneNumbers  $number
     * @param  string  $id
     *
     * @return mixed
     */

    public function release(PhoneNumbers $number, string $id);

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
    public function batchAvailable(array $ids);

    /**
     * @param  PhoneNumbers  $number
     * @param  array  $input
     *
     * @return mixed
     */
    public function payPayment(PhoneNumbers $number, array $input);
}
