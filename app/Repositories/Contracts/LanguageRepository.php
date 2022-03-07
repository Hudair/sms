<?php

namespace App\Repositories\Contracts;

/* *
 * Interface LanguageRepository
 */

use App\Models\Language;

interface LanguageRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  Language  $language
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Language $language, array $input);

    /**
     * @param  Language  $language
     *
     * @return mixed
     */

    public function destroy(Language $language);


    /**
     * download language
     *
     * @param  Language  $language
     *
     * @return mixed
     */
    public function download(Language $language);

    /**
     * upload language
     *
     * @param  array  $input
     * @param  Language  $language
     *
     * @return mixed
     */
    public function upload(array $input, Language $language);
}
