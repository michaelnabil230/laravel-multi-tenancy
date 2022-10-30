<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedException;
use MichaelNabil230\MultiTenancy\Tenancy;

abstract class IdentificationMiddleware
{
    /**
     * Set this property if you want to customize the on-fail behavior.
     *
     * @var callable|null
     */
    public static $onFail = null;

    public function __construct(protected Tenancy $tenancy)
    {
    }

    public function initializeTenancy($request, $next, $tenant)
    {
        try {
            tenancy()->initialize($tenant);
        } catch (TenantCouldNotBeIdentifiedException $e) {
            $onFail = static::$onFail ?? function ($e, $request, $next) {
                throw $e;
            };

            return $onFail($e, $request, $next);
        }

        return $next($request);
    }
}
