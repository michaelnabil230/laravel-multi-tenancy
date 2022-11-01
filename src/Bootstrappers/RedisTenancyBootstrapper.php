<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Redis;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class RedisTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var array<string, string> Original prefixes of connections */
    public array $originalPrefixes = [];

    public function __construct(
        protected Repository $config,
    ) {
    }

    public function bootstrap(Tenant $tenant)
    {
        foreach ($this->prefixedConnections() as $connection) {
            $prefix = config('multi-tenancy.redis.prefix_base').$tenant->getKey();
            $client = Redis::connection($connection)->client();

            $this->originalPrefixes[$connection] = $client->getOption($client::OPT_PREFIX);
            $client->setOption($client::OPT_PREFIX, $prefix);
        }
    }

    public function revert()
    {
        foreach ($this->prefixedConnections() as $connection) {
            $client = Redis::connection($connection)->client();

            $client->setOption($client::OPT_PREFIX, $this->originalPrefixes[$connection]);
        }

        $this->originalPrefixes = [];
    }

    protected function prefixedConnections()
    {
        return config('multi-tenancy.redis.prefixed_connections');
    }
}
