<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Domain as Model;

trait Domain
{
    /**
     * The domain model class name.
     */
    public static string $domainModel = Model::class;

    /**
     * Set the domain model class name.
     */
    public static function useDomainModel(string $domainModel): void
    {
        static::$domainModel = $domainModel;
    }

    /**
     * Get the domain model class name.
     */
    public static function domainModel(): string
    {
        return static::$domainModel;
    }

    /**
     * Get a new domain model instance.
     */
    public static function domain(): BaseModel
    {
        return new static::$domainModel;
    }
}
