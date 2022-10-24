<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class adminMiddlaware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!\Auth::user()->isAdmin()) {
            if ($request->ajax()) {
                return response('Admin account required.', 401);
            } else {
                //redirect to dashboard
                return redirect('/dashboard');

                // return redirect('/'); // set here any url you need
            }
        }
        return $next($request);
    }
}
