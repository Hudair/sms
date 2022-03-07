<?php

namespace App\Repositories\Contracts;

/* *
 * Interface SpamWordRepository
 */

use App\Models\SpamWord;

interface SpamWordRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  SpamWord  $spamWord
     *
     * @return mixed
     */

    public function destroy(SpamWord $spamWord);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchDestroy(array $ids);
}
