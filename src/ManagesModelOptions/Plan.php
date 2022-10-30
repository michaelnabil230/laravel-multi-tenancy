<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Plan as Model;

trait Plan
{
    /**
     * The plan model class name.
     *
     * @var string
     */
    public static $planModel = Model::class;

    /**
     * Set the plan model class name.
     *
     * @param  string  $planModel
     * @return void
     */
    public static function usePlanModel($planModel)
    {
        static::$planModel = $planModel;
    }

    /**
     * Get the plan model class name.
     *
     * @return string
     */
    public static function planModel()
    {
        return static::$planModel;
    }

    /**
     * Get a new plan model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function plan()
    {
        return new static::$planModel;
    }
}
