<?php

namespace MichaelNabil230\MultiTenancy\TenantFinder;

use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class TenantFinderByDomain extends TenantFinder
{
    public static function find(Request $request): Tenant
    {
        $domain = $request->getHost();

        return MultiTenancy::tenant()::query()
            ->whereRelation('domains', 'domain', $domain)
            ->firstOr(function () use ($domain) {
                throw new TenantCouldNotBeIdentifiedOnDomainException($domain);
            });
    }
}
