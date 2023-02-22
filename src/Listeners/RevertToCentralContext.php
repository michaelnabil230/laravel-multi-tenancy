<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use MichaelNabil230\MultiTenancy\Events;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class RevertToCentralContext
{
    /**
     * Handle the given event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Events\Tenancy\TenancyEnded  $event
     */
    public function handle(Events\Tenancy\TenancyEnded $event): void
    {
        event(new Events\RevertingToCentralContext($event->tenant));

        foreach (MultiTenancy::getBootstrappers() as $bootstrapper) {
            $bootstrapper->revert();
        }

        event(new Events\RevertedToCentralContext($event->tenant));
    }
}
