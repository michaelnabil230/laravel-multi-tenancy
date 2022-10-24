<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers\Contracts;

use MichaelNabil230\MultiTenancy\Models\Tenant;

interface TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant);

    public function revert();
}
