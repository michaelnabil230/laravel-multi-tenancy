<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Contracts\Auth\Authenticatable;

trait Owner
{
    /**
     * The owner model class name.
     *
     * @var string
     */
    public static string $ownerModel = 'App\Models\User';

    /**
     * Set the owner model class name.
     *
     * @param  string  $ownerModel
     * @return void
     */
    public static function useOwnerModel(string $ownerModel): void
    {
        static::$ownerModel = $ownerModel;
    }

    /**
     * Get the owner model class name.
     *
     * @return string
     */
    public static function ownerModel(): string
    {
        return static::$ownerModel;
    }

    /**
     * Get a new owner model instance.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public static function owner(): Authenticatable
    {
        return new static::$ownerModel;
    }
}
