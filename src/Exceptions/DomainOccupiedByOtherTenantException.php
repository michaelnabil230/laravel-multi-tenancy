<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Exception;

class DomainOccupiedByOtherTenantException extends Exception
{
    public function __construct($domain)
    {
        parent::__construct("The $domain domain is occupied by another tenant.");
    }
}
