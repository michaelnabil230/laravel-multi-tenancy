<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Tenant as Model;

trait Tenant
{
    /**
     * The tenant model class name.
     *
     * @var string
     */
    public static $tenantModel = Model::class;

    /**
     * Set the tenant model class name.
     *
     * @param  string  $tenantModel
     * @return void
     */
    public static function useTenantModel($tenantModel)
    {
        static::$tenantModel = $tenantModel;
    }

    /**
     * Get the tenant model class name.
     *
     * @return string
     */
    public static function tenantModel()
    {
        return static::$tenantModel;
    }

    /**
     * Get a new tenant model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function tenant()
    {
        return new static::$tenantModel;
    }
}
