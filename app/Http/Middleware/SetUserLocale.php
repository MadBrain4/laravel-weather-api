<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class SetUserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si está autenticado, usa su idioma
        if (Auth::check()) {
            $lang = Auth::user()->language ?? 'en';
        }
        // 2. Si viene el idioma en el body (por ejemplo, durante el registro)
        elseif ($request->has('language')) {
            $lang = $request->input('language');
        }
        // 3. Por defecto, usa 'en'
        else {
            $lang = 'en';
        }

        App::setLocale($lang);

        return $next($request);
    }
}
