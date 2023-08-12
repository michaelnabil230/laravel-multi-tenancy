<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelNabil230\MultiTenancy\Events\Domain as EventsDomain;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\Observers\DomainObserver;
use MichaelNabil230\MultiTenancy\Traits\IsSubdomain;

class Domain extends Model
{
    use IsSubdomain {
        isSubdomain as checkIsSubdomain;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain',
        'is_premium',
        'is_verified',
        'tenant_id',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
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
     */
    protected static function booted()
    {
        static::observe(DomainObserver::class);
    }

    /**
     * Get the tenant that owns the Domain
     */
    public function tenant(): BelongsTo
    {
        $model = MultiTenancy::tenantModel();

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    /**
     * Get the domain's isSubdomain.
     */
    protected function isSubdomain(): Attribute
    {
        return Attribute::get(fn () => $this->checkIsSubdomain($this->domain));
    }
}
