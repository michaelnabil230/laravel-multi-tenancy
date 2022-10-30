<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Exception;

class NotASubdomainException extends Exception
{
    public function __construct(string $hostname)
    {
        parent::__construct("Hostname $hostname does not include a subdomain.");
    }
}
