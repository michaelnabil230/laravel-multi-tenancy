<?php

namespace MichaelNabil230\MultiTenancy\Features\Contracts;

use MichaelNabil230\MultiTenancy\Tenancy;

interface Feature
{
    public function bootstrap(Tenancy $tenancy): void;
}
