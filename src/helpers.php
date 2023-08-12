<?php

use MichaelNabil230\MultiTenancy\MultiTenancy;

if (! function_exists('tenant')) {
    /**
     * Get a key from the current tenant's storage.
     *
     * @return \MichaelNabil230\MultiTenancy\Models\Tenant|mixed|null
     */
    function tenant(string $key = null)
    {
        if (is_null($key)) {
            return MultiTenancy::current();
        }

        return MultiTenancy::current()?->getAttribute($key) ?? null;
    }
}
