<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use MichaelNabil230\MultiTenancy\Exceptions\TenancyNotInitializedException;

abstract class IdentificationMiddleware
{
    /** @var callable */
    public static $onFail;

    public function initializeTenancy($request, $next, ...$resolverArguments)
    {
        try {
            tenancy()->initialize(...$resolverArguments);
        } catch (TenancyNotInitializedException $e) {
            $onFail = static::$onFail ?? function ($e) {
                throw $e;
            };

            return $onFail($e, $request, $next);
        }

        return $next($request);
    }
}
