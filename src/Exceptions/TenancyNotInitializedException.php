<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Exception;

class TenancyNotInitializedException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message ?: 'Tenancy is not initialized.');
    }
}
