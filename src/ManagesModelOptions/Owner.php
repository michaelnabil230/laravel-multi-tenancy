<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model;

trait Owner
{
    /**
     * The owner model class name.
     */
    public static string $ownerModel = 'App\Models\User';

    /**
     * Set the owner model class name.
     */
    public static function useOwnerModel(string $ownerModel): void
    {
        static::$ownerModel = $ownerModel;
    }

    /**
     * Get the owner model class name.
     */
    public static function ownerModel(): string
    {
        return static::$ownerModel;
    }

    /**
     * Get a new owner model instance.
     */
    public static function owner(): Model
    {
        return new static::$ownerModel;
    }
}
