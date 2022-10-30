<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelNabil230\MultiTenancy\Events\Tenant as EventsTenant;
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
        'saving' => EventsTenant\SavingTenant::class,
        'saved' => EventsTenant\TenantSaved::class,
        'creating' => EventsTenant\CreatingTenant::class,
        'created' => EventsTenant\TenantCreated::class,
        'updating' => EventsTenant\UpdatingTenant::class,
        'updated' => EventsTenant\TenantUpdated::class,
        'deleting' => EventsTenant\DeletingTenant::class,
        'deleted' => EventsTenant\TenantDeleted::class,
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
}
