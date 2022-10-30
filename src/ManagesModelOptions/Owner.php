<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

trait Owner
{
    /**
     * The owner model class name.
     *
     * @var string
     */
    public static $ownerModel = 'App\Models\User';

    /**
     * Set the owner model class name.
     *
     * @param  string  $ownerModel
     * @return void
     */
    public static function useOwnerModel($ownerModel)
    {
        static::$ownerModel = $ownerModel;
    }

    /**
     * Get the owner model class name.
     *
     * @return string
     */
    public static function ownerModel()
    {
        return static::$ownerModel;
    }

    /**
     * Get a new owner model instance.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public static function owner()
    {
        return new static::$ownerModel;
    }
}
