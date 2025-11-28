<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.available_locales', ['en', 'bn']);
        
        // Priority: URL parameter > Session > User preference > Browser > Default
        $locale = $request->get('lang') 
                  ?? Session::get('locale')
                  ?? $request->user()?->language
                  ?? $this->getPreferredLanguage($request, $availableLocales)
                  ?? config('app.locale');

        // Validate locale
        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            
            // Update user preference if authenticated
            if ($request->user() && $request->user()->language !== $locale) {
                $request->user()->update(['language' => $locale]);
            }
        }

        return $next($request);
    }

    /**
     * Get preferred language from Accept-Language header
     */
    protected function getPreferredLanguage(Request $request, array $availableLocales): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', $lang);
            $code = trim($parts[0]);
            $quality = 1.0;
            
            if (isset($parts[1]) && str_starts_with($parts[1], 'q=')) {
                $quality = (float) substr($parts[1], 2);
            }
            
            $languages[$code] = $quality;
        }
        
        arsort($languages);
        
        // Match with available locales
        foreach ($languages as $lang => $quality) {
            $shortLang = substr($lang, 0, 2);
            if (in_array($shortLang, $availableLocales)) {
                return $shortLang;
            }
        }
        
        return null;
    }
}
