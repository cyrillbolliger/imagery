<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    const ACCEPTED_LANGS = ['fr', 'de', 'en'];
    const FALLBACK_LANG = 'en';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('lang', $this->detectLocale());
    }

    private function detectLocale()
    {
        $lang = $this->getUserLang() ? $this->getUserLang() : $this->detectBrowserLang();
        return $lang ? $this->langToLocale($lang) : $this->getAppLocale();
    }

    private function getUserLang()
    {
        return Auth::user() ? Auth::user()->lang : false;
    }

    private function detectBrowserLang()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            $lang = self::FALLBACK_LANG;
        }

        if ($lang) {
            $lang = in_array($lang, self::ACCEPTED_LANGS) ? $lang : self::FALLBACK_LANG;
        }

        return $lang;
    }

    private function getAppLocale()
    {
        return str_replace('_', '-', app()->getLocale());
    }

    private function langToLocale(string $lang)
    {
        return $lang.'-'.strtoupper($lang);
    }
}
