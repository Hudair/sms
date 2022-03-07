<?php

namespace App\Repositories\Contracts;

/* *
 * Interface TemplateTagsRepository
 */

use App\Models\TemplateTags;

interface TemplateTagsRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  TemplateTags  $tags
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(TemplateTags $tags, array $input);

    /**
     * @param  TemplateTags  $tags
     *
     * @return mixed
     */

    public function destroy(TemplateTags $tags);

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
    public function batchRequired(array $ids);

    /**
     * @param  array  $ids
     *
     * @return mixed
     */
    public function batchOptional(array $ids);

}
