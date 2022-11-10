<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class TenantCouldNotBeIdentifiedOnDomainException extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct(string $domain)
    {
        parent::__construct("Tenant could not be identified on domain $domain");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified on this domain')
            ->setSolutionDescription('Did you forget to create a tenant for this domain?');
    }
}
