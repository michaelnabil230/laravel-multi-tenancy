<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class TenantCouldNotBeIdentifiedByRequestDataException extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct(string $tenantId)
    {
        parent::__construct("Tenant could not be identified by request data with payload: $tenantId");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified with this request data')
            ->setSolutionDescription('Did you forget to create a tenant with this id?');
    }
}
