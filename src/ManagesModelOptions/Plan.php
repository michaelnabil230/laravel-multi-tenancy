<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Plan as Model;

trait Plan
{
    /**
     * The plan model class name.
     */
    public static string $planModel = Model::class;

    /**
     * Set the plan model class name.
     */
    public static function usePlanModel(string $planModel): void
    {
        static::$planModel = $planModel;
    }

    /**
     * Get the plan model class name.
     */
    public static function planModel(): string
    {
        return static::$planModel;
    }

    /**
     * Get a new plan model instance.
     */
    public static function plan(): BaseModel
    {
        return new static::$planModel;
    }
}
