<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use MichaelNabil230\MultiTenancy\Models\Scopes\ParentModel;

trait BelongsToPrimaryModel
{
    abstract public function getRelationshipToPrimaryModel(): string;

    public static function bootBelongsToPrimaryModel()
    {
        static::addGlobalScope(new ParentModel);
    }
}
