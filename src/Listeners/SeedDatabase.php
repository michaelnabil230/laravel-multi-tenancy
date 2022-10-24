<?php

namespace MichaelNabil230\MultiTenancy\Listeners;

use Illuminate\Database\Seeder;
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
        [$class, $force] = config('multi-tenancy.seeder_parameters', []);

        if (! isset($class) && ! is_subclass_of(Seeder::class, $class)) {
            return;
        }

        if (! isset($force) && ! is_bool($force)) {
            return;
        }

        Artisan::call('db:seed', [
            '--class' => $class,
            '--force' => $force,
            '--tenant' => $tenant->tenant->getKey(),
        ]);
    }
}
