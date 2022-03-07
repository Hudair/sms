<?php

namespace App\Repositories\Contracts;

/* *
 * Interface TemplatesRepository
 */

use App\Models\Templates;

interface TemplatesRepository extends BaseRepository
{

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param Templates $template
     * @param array    $input
     *
     * @return mixed
     */
    public function update(Templates $template, array $input);

    /**
     * @param Templates $template
     *
     * @return mixed
     */

    public function destroy(Templates $template);

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
