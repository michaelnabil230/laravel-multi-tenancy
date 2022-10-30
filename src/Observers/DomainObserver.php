<?php

namespace MichaelNabil230\MultiTenancy\Observers;

use MichaelNabil230\MultiTenancy\Exceptions\DomainOccupiedByOtherTenantException;
use MichaelNabil230\MultiTenancy\Jobs\CheckDomainVerification;
use MichaelNabil230\MultiTenancy\Models\Domain;

class DomainObserver
{
    /**
     * Handle the Domain "saving" event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Domain  $self
     * @return void
     */
    public function saving(Domain $self)
    {
        $self->domain = strtolower($self->domain);

        if ($domain = $self->newQuery()->where('domain', $self->domain)->first()) {
            if ($domain->getKey() !== $self->getKey()) {
                throw new DomainOccupiedByOtherTenantException($self->domain);
            }
        }
    }

    /**
     * Handle the Domain "created" event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Domain  $domain
     * @return void
     */
    public function created(Domain $domain)
    {
        if ($domain->is_premium && ! $domain->is_subdomain) {
            CheckDomainVerification::dispatch($domain);
        }
    }

    /**
     * Handle the Domain "updated" event.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Domain  $domain
     * @return void
     */
    public function updated(Domain $domain)
    {
        $domain->update(['is_verified' => false]);

        if ($domain->is_premium && ! $domain->is_subdomain) {
            CheckDomainVerification::dispatch($domain);
        }
    }
}
