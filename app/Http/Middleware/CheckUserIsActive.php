<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('web')->check() && !auth()->guard('web')->user()->is_active) {
            auth()->guard('web')->logout();

            return redirect()->route('home', ['login' => 1])->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
