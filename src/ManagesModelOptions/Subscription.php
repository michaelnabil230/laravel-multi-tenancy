<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Subscription as Model;

trait Subscription
{
    /**
     * The subscription model class name.
     *
     * @var string
     */
    public static $subscriptionModel = Model::class;

    /**
     * Set the subscription model class name.
     *
     * @param  string  $subscriptionModel
     * @return void
     */
    public static function useSubscriptionModel($subscriptionModel)
    {
        static::$subscriptionModel = $subscriptionModel;
    }

    /**
     * Get the subscription model class name.
     *
     * @return string
     */
    public static function subscriptionModel()
    {
        return static::$subscriptionModel;
    }

    /**
     * Get a new subscription model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function subscription()
    {
        return new static::$subscriptionModel;
    }
}
