<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Illuminate\Database\Eloquent\Model;
class ApiForward
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
        if ($request->path() === 'api' && $request->method() !== 'POST') {
            throw new MethodNotAllowedException();
        }

        return $next($request);
    }
}
