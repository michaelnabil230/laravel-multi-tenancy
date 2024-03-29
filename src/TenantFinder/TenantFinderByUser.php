<?php

namespace MichaelNabil230\MultiTenancy\TenantFinder;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByUser;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class TenantFinderByUser extends TenantFinder
{
    public static function find(Request $request): Tenant
    {
        $user = $request->user();

        throw_unless($user instanceof Authenticatable, 'The user is not an instance of by `Authenticatable`');

        throw_if($user == null, new AuthenticationException);

        if (! is_null($tenant = $user->tenant)) {
            return $tenant;
        }

        throw new TenantCouldNotBeIdentifiedByUser;
    }
}
