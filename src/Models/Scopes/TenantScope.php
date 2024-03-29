<?php

namespace MichaelNabil230\MultiTenancy\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! MultiTenancy::checkCurrent()) {
            return;
        }

        $builder->where($model->qualifyColumn('tenant_id'), tenant()->getKey());
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
