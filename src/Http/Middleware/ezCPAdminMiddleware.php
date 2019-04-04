<?php

namespace hlaCk\ezCP\Http\Middleware;

use Closure;
use hlaCk\ezCP\Facades\ezCP;

class ezCPAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app('ezCPAuth')->guest()) {
            $user = app('ezCPAuth')->user();
            app()->setLocale($user->locale ?? app()->getLocale());

            return $user->hasPermission('browse_admin') ? $next($request) : redirect('/');
        }

        $urlLogin = route('ezcp.login');

        return redirect()->guest($urlLogin);
    }
}
