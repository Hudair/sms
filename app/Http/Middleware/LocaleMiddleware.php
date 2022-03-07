<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // available language in template array
        $availLocale = [
                'af' => 'af',
                'sq' => 'sq',
                'am' => 'am',
                'ar' => 'ar',
                'hy' => 'hy',
                'az' => 'az',
                'bn' => 'bn',
                'eu' => 'eu',
                'be' => 'be',
                'bg' => 'bg',
                'ca' => 'ca',
                'zh' => 'zh',
                'hr' => 'hr',
                'cs' => 'cs',
                'da' => 'da',
                'nl' => 'nl',
                'en' => 'en',
                'et' => 'et',
                'fi' => 'fi',
                'fr' => 'fr',
                'gl' => 'gl',
                'ka' => 'ka',
                'de' => 'de',
                'el' => 'el',
                'gu' => 'gu',
                'he' => 'he',
                'hi' => 'hi',
                'hu' => 'hu',
                'is' => 'is',
                'id' => 'id',
                'ga' => 'ga',
                'it' => 'it',
                'ja' => 'ja',
                'kk' => 'kk',
                'ko' => 'ko',
                'lv' => 'lv',
                'lt' => 'lt',
                'mk' => 'mk',
                'ms' => 'ms',
                'mn' => 'mn',
                'ne' => 'ne',
                'nb' => 'nb',
                'nn' => 'nn',
                'fa' => 'fa',
                'pl' => 'pl',
                'pt' => 'pt',
                'ro' => 'ro',
                'ru' => 'ru',
                'sr' => 'sr',
                'si' => 'si',
                'sk' => 'sk',
                'sl' => 'sl',
                'es' => 'es',
                'sw' => 'sw',
                'sv' => 'sv',
                'ta' => 'ta',
                'te' => 'te',
                'th' => 'th',
                'tr' => 'tr',
                'uk' => 'uk',
                'ur' => 'ur',
                'uz' => 'uz',
                'vi' => 'vi',
                'cy' => 'cy',
        ];

        // Locale is enabled and allowed to be change
        if (Session::has('locale') && array_key_exists(Session::get('locale'), $availLocale)) {
            // Set the Laravel locale
            app()->setLocale(Session::get('locale'));
        }

        return $next($request);
    }
}
