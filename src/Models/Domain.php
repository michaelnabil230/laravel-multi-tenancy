<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelNabil230\MultiTenancy\Events\Domain as EventsDomain;
use MichaelNabil230\MultiTenancy\Observers\DomainObserver;

class Domain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain',
        'tenant_id',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => EventsDomain\SavingDomain::class,
        'saved' => EventsDomain\DomainSaved::class,
        'creating' => EventsDomain\CreatingDomain::class,
        'created' => EventsDomain\DomainCreated::class,
        'updating' => EventsDomain\UpdatingDomain::class,
        'updated' => EventsDomain\DomainUpdated::class,
        'deleting' => EventsDomain\DeletingDomain::class,
        'deleted' => EventsDomain\DomainDeleted::class,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::observe(DomainObserver::class);
    }

    /**
     * Get the tenant that owns the Domain
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the order's domain.
     *
     * @return Attribute
     */
    protected function domain(): Attribute
    {
        return Attribute::set(fn () => strtolower($this->domain));
    }

    /**
     * Get the order's isSubdomain.
     *
     * @return Attribute
     */
    protected function isSubdomain(): Attribute
    {
        return Attribute::get(fn () => str_ends_with($this->domain, config('multi-tenancy.central_domains')));
    }
}
