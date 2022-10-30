<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Tenant;

abstract class TenantEvent
{
    use SerializesModels;

    public function __construct(public Tenant $tenant)
    {
    }
}
