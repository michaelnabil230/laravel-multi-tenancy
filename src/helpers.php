<?php

use MichaelNabil230\MultiTenancy\MultiTenancy;

if (! function_exists('tenant')) {
    /**
     * Get a key from the current tenant's storage.
     *
     * @param  string|null  $key
     * @return \MichaelNabil230\MultiTenancy\Models\Tenant|mixed|null
     */
    function tenant(string|null $key = null)
    {
        if (is_null($key)) {
            return MultiTenancy::current();
        }

        return MultiTenancy::current()?->getAttribute($key) ?? null;
    }
}
