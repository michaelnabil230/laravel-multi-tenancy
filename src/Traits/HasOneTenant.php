<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MichaelNabil230\MultiTenancy\MultiTenancy;

trait HasOneTenant
{
    /**
     * Get the tenant associated with the HasOneTenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tenant(): HasOne
    {
        return $this->hasOne(MultiTenancy::tenantModel(), 'owner_id');
    }
}
