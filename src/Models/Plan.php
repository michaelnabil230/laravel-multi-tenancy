<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MichaelNabil230\MultiTenancy\Enums\PeriodicityType;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\Traits\HasTranslations;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Plan extends Model
{
    use HasTranslatableSlug, HasFactory, SoftDeletes, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'price',
        'trial_interval',
        'trial_period',
        'invoice_interval',
        'invoice_period',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'invoice_period' => 'integer',
        'trial_period' => 'integer',
        'trial_interval' => PeriodicityType::class,
        'invoice_interval' => PeriodicityType::class,
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
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * The plan may belong to many features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(MultiTenancy::featureModel());
    }

    /**
     * The plan may have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MultiTenancy::subscriptionModel());
    }

    /**
     * Check if plan is free.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return (float) $this->price <= 0.00;
    }

    /**
     * Check if plan has trial.
     *
     * @return bool
     */
    public function hasTrial(): bool
    {
        return $this->trial_period && $this->trial_interval;
    }

    /**
     * Get plan feature by slug.
     *
     * @param  string  $slug
     * @return \MichaelNabil230\MultiTenancy\Models\Feature|null
     */
    public function getFeatureBySlug(string $slug): ?Feature
    {
        return $this->features()->where('slug', $slug)->first();
    }

    /**
     * Get if plan has feature by slug.
     *
     * @param  string  $slug
     * @return bool
     */
    public function hasFeature(string $slug): bool
    {
        return $this->features()->where('slug', $slug)->exists();
    }
}
