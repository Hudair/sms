<?php

namespace App\Repositories\Contracts;

/* *
 * Interface BlacklistsRepository
 */

use App\Models\Blacklists;

interface BlacklistsRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  Blacklists  $blacklists
     *
     * @return mixed
     */

    public function destroy(Blacklists $blacklists);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);
}
