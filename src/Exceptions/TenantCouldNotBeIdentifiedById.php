<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class TenantCouldNotBeIdentifiedById extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct($tenantId)
    {
        parent::__construct("Tenant could not be identified with tenant_id: $tenantId");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified with that ID')
            ->setSolutionDescription('Are you sure the ID is correct and the tenant exists?');
    }
}
