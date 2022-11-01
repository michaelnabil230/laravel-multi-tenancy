<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class TenantCouldNotBeIdentifiedByUser extends TenantCouldNotBeIdentifiedException implements ProvidesSolution
{
    public function __construct()
    {
        parent::__construct('Tenant could not be identified on user');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Tenant could not be identified on this user')
            ->setSolutionDescription('Did you forget to create a tenant for this user?');
    }
}
