<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelNabil230\MultiTenancy\MultiTenancy;

trait BelongsToPlan
{
    /**
     * The model always belongs to a plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        $model = MultiTenancy::planModel();

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    /**
     * Scope models by plan id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  int  $planId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlanId(Builder $builder, int $planId): Builder
    {
        return $builder->where('plan_id', $planId);
    }
}
