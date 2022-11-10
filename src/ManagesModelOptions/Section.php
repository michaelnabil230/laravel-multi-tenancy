<?php

namespace MichaelNabil230\MultiTenancy\ManagesModelOptions;

use Illuminate\Database\Eloquent\Model as BaseModel;
use MichaelNabil230\MultiTenancy\Models\Section as Model;

trait Section
{
    /**
     * The section model class name.
     *
     * @var string
     */
    public static string $sectionModel = Model::class;

    /**
     * Set the section model class name.
     *
     * @param  string  $sectionModel
     * @return void
     */
    public static function useSectionModel(string $sectionModel): void
    {
        static::$sectionModel = $sectionModel;
    }

    /**
     * Get the section model class name.
     *
     * @return string
     */
    public static function sectionModel(): string
    {
        return static::$sectionModel;
    }

    /**
     * Get a new section model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function section(): BaseModel
    {
        return new static::$sectionModel;
    }
}
