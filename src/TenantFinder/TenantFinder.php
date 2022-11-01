<?php

namespace MichaelNabil230\MultiTenancy\TenantFinder;

use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedException;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;

abstract class TenantFinder
{
    abstract public static function find(Request $request): Tenant;

    public static function findOrFail(Request $request): Tenant
    {
        try {
            return static::find($request)->initialize();
        } catch (TenantCouldNotBeIdentifiedException $e) {
            return MultiTenancy::onFail($e, $request);
        }
    }
}
