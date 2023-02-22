<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Section as Model;

trait Section
{
    /**
     * The section model class name.
     */
    public static string $sectionModel = Model::class;

    /**
     * Set the section model class name.
     */
    public static function useSectionModel(string $sectionModel): void
    {
        static::$sectionModel = $sectionModel;
    }

    /**
     * Get the section model class name.
     */
    public static function sectionModel(): string
    {
        return static::$sectionModel;
    }

    /**
     * Get a new section model instance.
     */
    public static function section(): BaseModel
    {
        return new static::$sectionModel;
    }
}
