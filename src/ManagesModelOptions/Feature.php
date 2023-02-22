<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Feature as Model;

trait Feature
{
    /**
     * The feature model class name.
     */
    public static string $featureModel = Model::class;

    /**
     * Set the feature model class name.
     */
    public static function useFeatureModel(string $featureModel): void
    {
        static::$featureModel = $featureModel;
    }

    /**
     * Get the feature model class name.
     */
    public static function featureModel(): string
    {
        return static::$featureModel;
    }

    /**
     * Get a new feature model instance.
     */
    public static function feature(): BaseModel
    {
        return new static::$featureModel;
    }
}
