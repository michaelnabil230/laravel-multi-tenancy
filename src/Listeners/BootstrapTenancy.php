<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use MichaelNabil230\MultiTenancy\Events\Tenancy;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class BootstrapTenancy
{
    public function handle(Tenancy\TenancyInitialized $event)
    {
        event(new Tenancy\BootstrappingTenancy($event->tenant));

        foreach (MultiTenancy::getBootstrappers() as $bootstrapper) {
            $bootstrapper->bootstrap($event->tenant);
        }

        event(new Tenancy\TenancyBootstrapped($event->tenant));
    }
}
