<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelNabil230\MultiTenancy\Models\Scopes\TenantScope;
use MichaelNabil230\MultiTenancy\MultiTenancy;

trait BelongsToTenant
{
    /**
     * The model always belongs to a tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        $model = MultiTenancy::tenantModel();

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    public static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (! $model->getAttribute('tenant_id') && ! $model->relationLoaded('tenant')) {
                if (MultiTenancy::checkCurrent()) {
                    $model->setAttribute('tenant_id', tenant()->getTenantKey());
                    $model->setRelation('tenant', tenant());
                }
            }
        });
    }
}
