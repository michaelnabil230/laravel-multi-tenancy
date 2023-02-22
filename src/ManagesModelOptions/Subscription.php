<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Subscription as Model;

trait Subscription
{
    /**
     * The subscription model class name.
     */
    public static string $subscriptionModel = Model::class;

    /**
     * Set the subscription model class name.
     */
    public static function useSubscriptionModel(string $subscriptionModel): void
    {
        static::$subscriptionModel = $subscriptionModel;
    }

    /**
     * Get the subscription model class name.
     */
    public static function subscriptionModel(): string
    {
        return static::$subscriptionModel;
    }

    /**
     * Get a new subscription model instance.
     */
    public static function subscription(): BaseModel
    {
        return new static::$subscriptionModel;
    }
}
