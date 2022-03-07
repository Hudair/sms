<?php

namespace App\Repositories\Contracts;

/* *
 * Interface CurrencyRepository
 */

use App\Models\Currency;

interface CurrencyRepository extends BaseRepository
{

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param Currency $currency
     * @param array    $input
     *
     * @return mixed
     */
    public function update(Currency $currency, array $input);

    /**
     * @param Currency $currency
     *
     * @return mixed
     */

    public function destroy(Currency $currency);

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

}
