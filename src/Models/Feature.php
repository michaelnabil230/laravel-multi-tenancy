<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\Traits\BelongsToPlan;
use MichaelNabil230\MultiTenancy\Traits\HasTranslations;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Feature extends Model
{
    use HasTranslatableSlug, HasFactory, SoftDeletes, BelongsToPlan, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'slug',
        'name',
        'description',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        'slug',
        'name',
        'description',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * The feature may belong to many plans.
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(MultiTenancy::planModel());
    }
}
