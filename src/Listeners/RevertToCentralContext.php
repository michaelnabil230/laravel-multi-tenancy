<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use MichaelNabil230\MultiTenancy\Events;

class RevertToCentralContext
{
    public function handle(Events\Tenancy\TenancyEnded $event)
    {
        event(new Events\RevertingToCentralContext($event->tenancy));

        foreach ($event->tenancy->getBootstrappers() as $bootstrapper) {
            $bootstrapper->revert();
        }

        event(new Events\RevertedToCentralContext($event->tenancy));
    }
}
