<?php

namespace MichaelNabil230\MultiTenancy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MichaelNabil230\MultiTenancy\Tenancy
 */
class MultiTenancy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \MichaelNabil230\MultiTenancy\Tenancy::class;
    }
}
