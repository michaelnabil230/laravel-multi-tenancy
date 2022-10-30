<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use Illuminate\Support\Facades\Artisan;
use MichaelNabil230\MultiTenancy\Events\Tenant\TenantCreated;

class SeedDatabase
{
    /**
     * Handle the given event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Events\Tenant\TenantCreated  $tenant
     * @return void
     */
    public function handle(TenantCreated $tenant)
    {
        Artisan::call('tenants:seed', [
            '--tenants' => [$tenant->tenant->getKey()],
        ]);
    }
}
