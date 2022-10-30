<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Domain;

abstract class DomainEvent
{
    use SerializesModels;

    public function __construct(public Domain $domain)
    {
    }
}
