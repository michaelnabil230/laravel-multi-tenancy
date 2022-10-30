<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use MichaelNabil230\MultiTenancy\Exceptions\RouteIsMissingTenantParameterException;

class InitializeTenancyByPath extends IdentificationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Route $route */
        $route = $request->route();

        // Only initialize tenancy if tenant is the first parameter
        // We don't want to initialize tenancy if the tenant is
        // simply injected into some route controller action.
        if ($route->parameterNames()[0] !== 'tenant') {
            throw new RouteIsMissingTenantParameterException;
        }

        return $this->initializeTenancy(
            $request,
            $next,
            $route
        );
    }
}
