<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Tenant;

abstract class ArtisanTenantEvent
{
    use SerializesModels;

    public function __construct(
        public string $artisanCommand,
        public Tenant $tenant,
    ) {
    }
}
