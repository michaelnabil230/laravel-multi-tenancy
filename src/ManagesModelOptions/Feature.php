<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Feature as Model;

trait Feature
{
    /**
     * The feature model class name.
     *
     * @var string
     */
    public static $featureModel = Model::class;

    /**
     * Set the feature model class name.
     *
     * @param  string  $featureModel
     * @return void
     */
    public static function useFeatureModel($featureModel)
    {
        static::$featureModel = $featureModel;
    }

    /**
     * Get the feature model class name.
     *
     * @return string
     */
    public static function featureModel()
    {
        return static::$featureModel;
    }

    /**
     * Get a new feature model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function feature()
    {
        return new static::$featureModel;
    }
}
