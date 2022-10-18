<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MichaelNabil230\MultiTenancy\Events\Tenant as EventsTenant;

class Tenant extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data',
        'owner_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
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
        return $this->hasMany(Domain::class);
    }

    /**
     * Get the owner that owns the Tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('multi-tenancy.owner_model', \App\Models\User::class), 'owner_id');
    }

    public function createDomain($data): Domain
    {
        if (! is_array($data)) {
            $data = ['domain' => $data];
        }

        $domain = (new Domain)->fill($data);
        $domain->tenant()->associate($this);
        $domain->save();

        return $domain;
    }
}
