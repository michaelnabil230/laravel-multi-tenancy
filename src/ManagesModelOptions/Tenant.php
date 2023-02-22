<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Tenant as Model;

trait Tenant
{
    /**
     * The tenant model class name.
     */
    public static string $tenantModel = Model::class;

    /**
     * Set the tenant model class name.
     */
    public static function useTenantModel(string $tenantModel): void
    {
        static::$tenantModel = $tenantModel;
    }

    /**
     * Get the tenant model class name.
     */
    public static function tenantModel(): string
    {
        return static::$tenantModel;
    }

    /**
     * Get a new tenant model instance.
     */
    public static function tenant(): BaseModel
    {
        return new static::$tenantModel;
    }
}
