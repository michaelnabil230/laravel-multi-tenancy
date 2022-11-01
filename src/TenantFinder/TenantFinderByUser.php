<?php

namespace MichaelNabil230\MultiTenancy\TenantFinder;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByUser;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class TenantFinderByUser extends TenantFinder
{
    public static function find(Request $request): Tenant
    {
        $user = Auth::user();

        if (is_null($user)) {
            throw new AuthenticationException;
        }

        $tenant = $user->tenant;

        if (! is_null($tenant)) {
            return $tenant;
        }

        throw new TenantCouldNotBeIdentifiedByUser;
    }
}
