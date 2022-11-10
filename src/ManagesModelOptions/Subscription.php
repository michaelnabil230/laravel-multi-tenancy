<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Subscription as Model;

trait Subscription
{
    /**
     * The subscription model class name.
     *
     * @var string
     */
    public static string $subscriptionModel = Model::class;

    /**
     * Set the subscription model class name.
     *
     * @param  string  $subscriptionModel
     * @return void
     */
    public static function useSubscriptionModel(string $subscriptionModel): void
    {
        static::$subscriptionModel = $subscriptionModel;
    }

    /**
     * Get the subscription model class name.
     *
     * @return string
     */
    public static function subscriptionModel(): string
    {
        return static::$subscriptionModel;
    }

    /**
     * Get a new subscription model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function subscription(): BaseModel
    {
        return new static::$subscriptionModel;
    }
}
