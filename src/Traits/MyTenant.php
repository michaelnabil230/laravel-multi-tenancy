<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MichaelNabil230\MultiTenancy\Models\Tenant;

trait MyTenant
{
    /**
     * Get the tenant associated with the MyTenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(config('multi-tenancy.tenant_model', Tenant::class), 'owner_id');
    }
}
