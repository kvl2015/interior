<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $fullLocale = explode('_', $request->segment(1));
        app()->setLocale($fullLocale[1] ? $fullLocale[1] : 'en');
        setCountry($fullLocale[0]);

        //app()->setLocale($request->segment(1));
        //app()->setLocale('en');
        $request->route()->forgetParameter('locale');
        return $next($request);
    }
}
