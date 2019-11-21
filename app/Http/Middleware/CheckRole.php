<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::user()->hasRole($role)) {
            return redirect()->back()->withErrors(['role_error' => 'You can not access to this page.']);
        }
        return $next($request);
    }
}
