<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use MichaelNabil230\MultiTenancy\Events;

class BootstrapTenancy
{
    public function handle(Events\TenancyInitialized $event)
    {
        event(new Events\BootstrappingTenancy($event->tenancy));

        // Handel any think

        event(new Events\TenancyBootstrapped($event->tenancy));
    }
}
