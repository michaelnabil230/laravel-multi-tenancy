<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use MichaelNabil230\MultiTenancy\Exceptions\TenancyNotInitializedException;

abstract class IdentificationMiddleware
{
    /** @var callable */
    public static $onFail;

    public function initializeTenancy($request, $next, $tenant)
    {
        try {
            tenancy()->initialize($tenant);
        } catch (TenancyNotInitializedException $e) {
            $onFail = static::$onFail ?? function ($e, $request, $next) {
                throw $e;
            };

            return $onFail($e, $request, $next);
        }

        return $next($request);
    }
}
