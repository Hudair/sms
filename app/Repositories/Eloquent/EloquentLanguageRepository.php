<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\Language;
use App\Repositories\Contracts\LanguageRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Madnest\Madzipper\Facades\Madzipper;
use Throwable;
use ZipArchive;

class EloquentLanguageRepository extends EloquentBaseRepository implements LanguageRepository
{
    /**
     * EloquentLanguageRepository constructor.
     *
     * @param  Language  $language
     */
    public function __construct(Language $language)
    {
        parent::__construct($language);
    }

    /**
     * @param  array  $input
     *
     * @return Language|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input): Language
    {
        /** @var Language $language */

        $lang_index       = array_search($input['language'], array_column(Language::languageCodes(), 'code'));
        $lang             = Language::languageCodes()[$lang_index];
        $lang['iso_code'] = strtolower($input['country']);
        $lang['status']   = $input['status'];

        if ( ! is_dir(base_path('resources/lang/'.$lang['code']))) {
            File::copyDirectory(base_path("resources/lang/en"), base_path("resources/lang/").$lang['code']);
        }

        $language = $this->make(Arr::only($lang, ['name', 'code', 'iso_code', 'status']));

        if ( ! $this->save($language)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $language;

    }


    /**
     * download language file
     *
     * @param  Language  $language
     *
     * @return mixed|string
     * @throws Exception
     */
    public function download(Language $language): string
    {
        $files = glob($language->languageDir()."*");
        $zip   = storage_path("tmp/language-".$language->code.".zip");
        Madzipper::make($zip)->add($files)->close();

        return $zip;
    }

    /**
     * upload language files
     *
     * @param  array  $input
     * @param  Language  $language
     *
     * @return bool|mixed
     * @throws GeneralException
     */
    public function upload(array $input, Language $language): bool
    {

        // move file to temp place
        $tmp_path  = storage_path('tmp');
        $file_name = 'language-package';
        $input['file']->move($tmp_path, $file_name);

        // after moving, request['file'] will no longer be there
        $tmp_zip = storage_path("tmp/{$file_name}");

        $zip = new ZipArchive();

        $openZip = $zip->open($tmp_zip, ZipArchive::CREATE);

        // read zip file check if zip archive invalid
        if ($openZip !== true) {
            throw new GeneralException(__('locale.settings.invalid_zip'));
        }

        // unzip template archive and remove zip file
        $zip->extractTo($language->languageDir());
        $zip->close();
        unlink($tmp_zip);

        return true;

    }

    /**
     * @param  Language  $language
     *
     * @return bool
     */
    private function save(Language $language): bool
    {
        if ( ! $language->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Language  $language
     * @param  array  $input
     *
     * @return Language
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(Language $language, array $input): Language
    {
        if ( ! $language->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $language;
    }

    /**
     * @param  Language  $language
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Language $language)
    {
        if ( ! $language->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }
}
