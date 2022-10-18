<?php

use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\Tenancy;

if (! function_exists('tenancy')) {
    /**
     * Get the current tenancy storage.
     *
     * @return \MichaelNabil230\MultiTenancy\Tenancy
     */
    function tenancy(): Tenancy
    {
        return app(Tenancy::class);
    }
}

if (! function_exists('tenant')) {
    /**
     * Get a key from the current tenant's storage.
     *
     * @param  string|null  $key
     * @return \MichaelNabil230\MultiTenancy\Models\Tenant|null|mixed
     */
    function tenant($key = null)
    {
        if (! app()->bound(Tenant::class)) {
            return;
        }

        if (is_null($key)) {
            return app(Tenant::class);
        }

        return optional(app(Tenant::class))->getAttribute($key) ?? null;
    }
}
