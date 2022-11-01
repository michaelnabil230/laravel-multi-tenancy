<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelNabil230\MultiTenancy\Events;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\Traits\HasSubscriptions;
use MichaelNabil230\MultiTenancy\Traits\HasValidationRules;

class Tenant extends Model
{
    use HasFactory, HasUuids, HasSubscriptions, HasValidationRules;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data',
        'owner_id',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'saving' => Events\Tenant\SavingTenant::class,
        'saved' => Events\Tenant\TenantSaved::class,
        'creating' => Events\Tenant\CreatingTenant::class,
        'created' => Events\Tenant\TenantCreated::class,
        'updating' => Events\Tenant\UpdatingTenant::class,
        'updated' => Events\Tenant\TenantUpdated::class,
        'deleting' => Events\Tenant\DeletingTenant::class,
        'deleted' => Events\Tenant\TenantDeleted::class,
    ];

    /**
     * Get all of the domains for the Tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains(): HasMany
    {
        return $this->hasMany(MultiTenancy::domainModel());
    }

    /**
     * Get the owner that owns the Tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(MultiTenancy::ownerModel(), 'owner_id');
    }

    /**
     * Initializes the tenant.
     *
     * @return static
     */
    public function initialize(): static
    {
        event(new Events\Tenancy\InitializingTenancy($this));

        if ($this->isCurrent()) {
            return $this;
        }

        // MultiTenancy::forgetCurrent();

        MultiTenancy::bindAsCurrentTenant($this);

        event(new Events\Tenancy\TenancyInitialized($this));

        return $this;
    }

    public function isCurrent(): bool
    {
        return MultiTenancy::current()?->getKey() === $this->getKey();
    }

    public function forget(): static
    {
        event(new Events\Tenancy\EndingTenancy($this));

        app()->forgetInstance(MultiTenancy::$containerKey);

        event(new Events\Tenancy\TenancyEnded($this));

        return $this;
    }

    public function execute(callable $callable)
    {
        $originalCurrentTenant = MultiTenancy::current();

        $this->initialize();

        return tap($callable($this), static function () use ($originalCurrentTenant) {
            $originalCurrentTenant
                ? $originalCurrentTenant->initialize()
                : MultiTenancy::forgetCurrent();
        });
    }
}
