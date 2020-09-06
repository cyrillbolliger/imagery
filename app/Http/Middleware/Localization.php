<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Localization
{
    const ACCEPTED_LANGS = ['fr', 'de', 'zu', 'en'];
    const FALLBACK_LANG = 'en';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        App::setLocale($this->detectLang());

        return $next($request);
    }

    /**
     * Detect optimal language
     *
     * Check in the following order:
     * 1. query parameter 'lang'
     * 2. authenticated user with a defined language in his user profile
     * 3. browser language
     * 4. fallback language
     *
     * @return string
     */
    private function detectLang(): string
    {
        if (request('lang', false)) {
            $lang = request('lang');
        } elseif ($this->getUserLang()) {
            $lang = $this->getUserLang();
        } else {
            $lang = $this->detectBrowserLang();
        }

        $lang = $this->isAcceptedLang($lang) ? $lang : self::FALLBACK_LANG;

        return $lang;
    }

    private function getUserLang()
    {
        $user = Auth::user();
        return $user instanceof \App\User ? $user->lang : false;
    }

    private function detectBrowserLang()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            $lang = self::FALLBACK_LANG;
        }

        return $lang;
    }

    private function isAcceptedLang($lang)
    {
        return in_array($lang, self::ACCEPTED_LANGS);
    }
}
