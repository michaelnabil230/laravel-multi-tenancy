<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Exception;

class RouteIsMissingTenantParameterException extends Exception
{
    public function __construct()
    {
        parent::__construct("The route's first argument is not the tenant id (configured parameter name: tenant).");
    }
}
