<?php

namespace MichaelNabil230\MultiTenancy\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ParentModel implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (! tenancy()->initialized) {
            return;
        }

        $builder->whereHas($builder->getModel()->getRelationshipToPrimaryModel());
    }

    public function extend(Builder $builder)
    {
        $builder->macro('withoutParentModel', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
