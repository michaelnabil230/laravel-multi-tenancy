<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByRequest;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByRequestData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        TenantFinderByRequest::findOrFail($request);

        return $next($request);
    }
}
