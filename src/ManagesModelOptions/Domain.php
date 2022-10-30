<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Domain as Model;

trait Domain
{
    /**
     * The domain model class name.
     *
     * @var string
     */
    public static $domainModel = Model::class;

    /**
     * Set the domain model class name.
     *
     * @param  string  $domainModel
     * @return void
     */
    public static function useDomainModel($domainModel)
    {
        static::$domainModel = $domainModel;
    }

    /**
     * Get the domain model class name.
     *
     * @return string
     */
    public static function domainModel()
    {
        return static::$domainModel;
    }

    /**
     * Get a new domain model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function domain()
    {
        return new static::$domainModel;
    }
}
