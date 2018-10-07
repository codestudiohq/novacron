<?php

namespace Studio\Novacron\Http\Middleware;

use Studio\Novacron\Novacron;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(Novacron::class)->authorize($request) ? $next($request) : abort(403);
    }
}
