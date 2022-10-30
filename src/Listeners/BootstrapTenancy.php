<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use MichaelNabil230\MultiTenancy\Events\Tenancy;

class BootstrapTenancy
{
    public function handle(Tenancy\TenancyInitialized $event)
    {
        event(new Tenancy\BootstrappingTenancy($event->tenancy));

        foreach ($event->tenancy->getBootstrappers() as $bootstrapper) {
            $bootstrapper->bootstrap($event->tenancy->tenant);
        }

        event(new Tenancy\TenancyBootstrapped($event->tenancy));
    }
}
