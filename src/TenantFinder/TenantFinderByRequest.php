<?php

namespace MichaelNabil230\MultiTenancy\TenantFinder;

use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByRequestDataException;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class TenantFinderByRequest extends TenantFinder
{
    public static string $header = 'X-Tenant';

    public static string $queryParameter = 'tenant';

    public static string $tenantParameterName = 'tenant';

    public static function find(Request $request): Tenant
    {
        [$id, $byRoute] = (new self)->getPayload($request);

        throw_if(is_null($id), 'The id is null given');

        return MultiTenancy::tenant()::query()
            ->where('id', $id)
            ->firstOr(function () use ($id, $byRoute) {
                throw_if($byRoute, new TenantCouldNotBeIdentifiedByPathException($id));

                throw new TenantCouldNotBeIdentifiedByRequestDataException($id);
            });
    }

    private function getPayload(Request $request): array
    {
        $tenant = null;

        $byRoute = false;

        if (self::$header && $request->hasHeader(self::$header)) {
            $tenant = $request->header(self::$header);
        } elseif (self::$queryParameter && $request->has(self::$queryParameter)) {
            $tenant = $request->get(self::$queryParameter);
        } else {
            $tenant = $request->route(self::$tenantParameterName);

            $request->route()->forgetParameter(self::$tenantParameterName);

            $byRoute = true;
        }

        return [$tenant, $byRoute];
    }
}
