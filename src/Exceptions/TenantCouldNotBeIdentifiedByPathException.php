<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class TenantCouldNotBeIdentifiedByPathException extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct(string $tenantId)
    {
        parent::__construct("Tenant could not be identified on path with tenant_id: $tenantId");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified on this path')
            ->setSolutionDescription('Did you forget to create a tenant for this path?');
    }
}
