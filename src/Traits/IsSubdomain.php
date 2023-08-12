<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Support\Str;

trait IsSubdomain
{
    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('multi-tenancy.central_domains'));
    }
}
