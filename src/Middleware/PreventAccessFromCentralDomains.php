<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class PreventAccessFromCentralDomains
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getHost(), config('multi-tenancy.central_domains'))) {
            return MultiTenancy::onFail(abort(404), $request);
        }

        return $next($request);
    }
}
