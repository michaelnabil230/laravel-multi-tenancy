<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Database\Eloquent\Model;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class Tenancy
{
    public Tenant|Model|null $tenant;

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
}
