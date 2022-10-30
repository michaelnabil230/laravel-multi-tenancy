<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use MichaelNabil230\MultiTenancy\Tenancy;

abstract class TenancyEvent
{
    public function __construct(public Tenancy $tenancy)
    {
    }
}
