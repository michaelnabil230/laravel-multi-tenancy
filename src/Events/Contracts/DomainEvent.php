<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Domain;

abstract class DomainEvent
{
    use SerializesModels;

    /** @var Domain */
    public $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }
}
