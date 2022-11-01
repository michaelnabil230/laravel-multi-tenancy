<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use MichaelNabil230\MultiTenancy\Models\Tenant;

abstract class TenancyEvent
{
    public function __construct(public Tenant $tenant)
    {
    }
}
