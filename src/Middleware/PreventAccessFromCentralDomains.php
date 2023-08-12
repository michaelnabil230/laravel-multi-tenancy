<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessFromCentralDomains
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->getHost(), config('multi-tenancy.central_domains'))) {
            return MultiTenancy::onFail(abort(404), $request);
        }

        return $next($request);
    }
}
