<?php

namespace MichaelNabil230\MultiTenancy\Observers;

use MichaelNabil230\MultiTenancy\Exceptions\DomainOccupiedByOtherTenantException;
use MichaelNabil230\MultiTenancy\Jobs\CheckDomainVerification;
use MichaelNabil230\MultiTenancy\Models\Domain;

class DomainObserver
{
    /**
     * Handle the Domain "saving" event.
     */
    public function saving(Domain $self): void
    {
        $self->domain = strtolower($self->domain);

        if ($domain = $self->newQuery()->where('domain', $self->domain)->first()) {
            throw_if(
                $domain->getKey() !== $self->getKey(),
                new DomainOccupiedByOtherTenantException($self->domain),
            );
        }
    }

    /**
     * Handle the Domain "created" event.
     */
    public function created(Domain $domain): void
    {
        if ($domain->is_premium && ! $domain->is_subdomain) {
            CheckDomainVerification::dispatch($domain);
        }
    }

    /**
     * Handle the Domain "updating" event.
     */
    public function updating(Domain $domain): void
    {
        $domain->update(['is_verified' => false]);

        if ($domain->is_premium && ! $domain->is_subdomain) {
            CheckDomainVerification::dispatch($domain);
        }
    }
}
