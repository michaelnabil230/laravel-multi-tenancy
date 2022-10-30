<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use MichaelNabil230\MultiTenancy\Models\Section as Model;

trait Section
{
    /**
     * The section model class name.
     *
     * @var string
     */
    public static $sectionModel = Model::class;

    /**
     * Set the section model class name.
     *
     * @param  string  $sectionModel
     * @return void
     */
    public static function useSectionModel($sectionModel)
    {
        static::$sectionModel = $sectionModel;
    }

    /**
     * Get the section model class name.
     *
     * @return string
     */
    public static function sectionModel()
    {
        return static::$sectionModel;
    }

    /**
     * Get a new section model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function section()
    {
        return new static::$sectionModel;
    }
}
