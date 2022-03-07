<?php

namespace App\Repositories\Contracts;

/* *
 * Interface KeywordRepository
 */

use App\Models\Keywords;

interface KeywordRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function store(array $input, array $billingCycle);

    /**
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return mixed
     */
    public function update(Keywords $keyword, array $input, array $billingCycle);

    /**
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @return mixed
     */
    public function updateByCustomer(Keywords $keyword, array $input);

    /**
     * @param Keywords $keyword
     *
     * @return mixed
     */

    public function destroy(Keywords $keyword);

    /**
     * @param  Keywords  $keyword
     * @param  string  $id
     *
     * @return mixed
     */

    public function release(Keywords $keyword, string $id);

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
    public function batchAvailable(array $ids);


    /**
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @return mixed
     */
    public function payPayment(Keywords $keyword, array $input);
}
