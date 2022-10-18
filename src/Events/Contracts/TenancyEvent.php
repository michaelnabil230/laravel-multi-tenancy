<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use MichaelNabil230\MultiTenancy\Tenancy;

abstract class TenancyEvent
{
    public Tenancy $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }
}
