<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Exceptions\NotAdminException;

class AdminMiddleware
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
        try
        {
            $user = Auth::User();
            if ($user->role_id != ADMIN)
            {
                throw new NotAdminException;
            }
        }
        catch (NotAdminException $e)
        {
            abort($e->status(), $e->message());
        }

        return $next($request);
    }
}
