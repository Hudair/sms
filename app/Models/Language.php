<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $uid)
 * @method static cursor()
 * @method static create(string[] $lan)
 * @property mixed       name
 * @property false|mixed status
 * @property mixed       code
 */

class Language extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'iso_code', 'status',
    ];

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();
        // Create uid when creating list.
        static::creating(function ($item) {
            $item->status = true;
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * all language code.
     *
     * @return array
     */
    public static function languageCodes(): array
    {
        $arr = [
            'af' => 'Afrikaans',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'hy' => 'Armenian',
            'az' => 'Azerbaijan',
            'bn' => 'Bengali',
            'eu' => 'Basque',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'ca' => 'Catalan',
            'zh' => 'Chinese',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'nl' => 'Dutch',
            'en' => 'English',
            'et' => 'Estonian',
            'fi' => 'Finnish',
            'fr' => 'French',
            'gl' => 'Galician',
            'ka' => 'Georgian',
            'de' => 'German',
            'el' => 'Greek',
            'gu' => 'Gujarati',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'id' => 'Indonesian',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'kk' => 'Kazakh',
            'ko' => 'Korean',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'mk' => 'Macedonian',
            'ms' => 'Malay',
            'mn' => 'Mongolian',
            'ne' => 'Nepali',
            'nb' => 'Norwegian-Bokmal',
            'nn' => 'Norwegian-Nynorsk',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sr' => 'Serbian',
            'si' => 'Sinhala',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'es' => 'Spanish',
            'sw' => 'Swahili',
            'sv' => 'Swedish',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'cy' => 'Welsh',
        ];

        $result = [];
        foreach ($arr as $key => $name) {
            $result[] = [
                'name' => $name,
                'code' => $key,
            ];
        }

        return $result;
    }

    /**
     * Disable language
     *
     * @return void
     */
    public function disable()
    {
        $this->status = false;
        $this->save();
    }

    /**
     * Enable language
     *
     * @return void
     */
    public function enable()
    {
        $this->status = true;
        $this->save();
    }

    /**
     * Language folder path.
     *
     * @return string
     */
    public function languageDir(): string
    {
        return base_path('resources/lang/'.$this->code.'/');
    }

}
