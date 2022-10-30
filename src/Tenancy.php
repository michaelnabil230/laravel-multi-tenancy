<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class Tenancy
{
    public Tenant|Model|null $tenant = null;

    public bool $initialized = false;

    /**
     * Initializes the tenant.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant|int|string  $tenant
     * @return void
     */
    public function initialize(Tenant|int|string $tenant): void
    {
        if (! is_object($tenant)) {
            $tenantId = $tenant;
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                throw new TenantCouldNotBeIdentifiedById($tenantId);
            }
        }

        if ($this->initialized && $this->tenant->getKey() === $tenant->getKey()) {
            return;
        }

        if ($this->initialized) {
            $this->end();
        }

        $this->tenant = $tenant;

        event(new Events\InitializingTenancy($this));

        $this->initialized = true;

        event(new Events\TenancyInitialized($this));
    }

    /**
     * End the tenant.
     *
     * @return void
     */
    public function end(): void
    {
        event(new Events\EndingTenancy($this));

        if (! $this->initialized) {
            return;
        }

        event(new Events\TenancyEnded($this));

        $this->initialized = false;

        $this->tenant = null;
    }

    /**
     * Run a callback for multiple tenants.
     * More performant than running $tenant->run() one by one.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant[]|\Traversable|string[]|null  $tenants
     * @param  callable  $callback
     * @return void
     */
    public function runForMultiple($tenants, callable $callback)
    {
        // Convert null to all tenants
        $tenants = is_null($tenants) ? Tenant::cursor() : $tenants;

        $tenants = Arr::wrap($tenants);

        // Use all tenants if $tenants are falsely
        $tenants = $tenants ?: Tenant::cursor();

        $originalTenant = $this->tenant;

        foreach ($tenants as $tenant) {
            if (! $tenant instanceof Tenant) {
                $tenant = Tenant::find($tenant);
            }

            $this->initialize($tenant);
            $callback($tenant);
        }

        if ($originalTenant) {
            $this->initialize($originalTenant);
        } else {
            $this->end();
        }
    }
}
