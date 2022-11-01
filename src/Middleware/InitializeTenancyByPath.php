<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\RouteIsMissingTenantParameterException;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByRequest;

class InitializeTenancyByPath
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
        // Only initialize tenancy if tenant is the first parameter
        // We don't want to initialize tenancy if the tenant is
        // simply injected into some route controller action.
        if ($request->route()->parameterNames()[0] !== TenantFinderByRequest::$tenantParameterName) {
            throw new RouteIsMissingTenantParameterException;
        }

        TenantFinderByRequest::findOrFail($request);

        return $next($request);
    }
}
