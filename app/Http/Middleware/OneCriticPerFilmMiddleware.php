<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Exceptions\MoreThanOneCriticException;

class OneCriticPerFilmMiddleware
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
            $film = $request->film_id;

            $userAlreadyMadeCritic = false;
            foreach($user->critics as $critic)
            {
                if ($critic->film_id == $film)
                {
                    $userAlreadyMadeCritic = true;
                }
            }

            if ($userAlreadyMadeCritic == true)
            {
                throw new MoreThanOneCriticException;
            }
        }
        catch (MoreThanOneCriticException $e)
        {
            abort($e->status(), $e->message());
        }

        return $next($request);
    }
}
