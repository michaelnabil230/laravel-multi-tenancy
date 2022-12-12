<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use Illuminate\Support\Facades\Artisan;
use MichaelNabil230\MultiTenancy\Events\Tenant\TenantCreated;

class SeedDatabase
{
    /**
     * Handle the given event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Events\Tenant\TenantCreated  $event
     * @return void
     */
    public function handle(TenantCreated $event): void
    {
        Artisan::call('tenants:seed', [
            '--tenant' => $event->tenant->getKey(),
        ]);
    }
}
