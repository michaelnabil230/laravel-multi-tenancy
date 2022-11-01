<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class CacheTenancyBootstrapper implements TenancyBootstrapper
{
    protected ?string $originalPrefix;

    protected string $storeName;

    protected string $cacheKeyBase;

    public function __construct(
        protected Application $app,
    ) {
        $this->originalPrefix = config('cache.prefix');

        $this->storeName = config('cache.default');

        $this->cacheKeyBase = config('multi-tenancy.cache_prefix_key', 'tenant_id_');
    }

    public function bootstrap(Tenant $tenant): void
    {
        $this->setCachePrefix($this->cacheKeyBase.$tenant->getKey());
    }

    public function revert(): void
    {
        $this->setCachePrefix($this->originalPrefix);
    }

    protected function setCachePrefix(string $prefix)
    {
        config()->set('cache.prefix', $prefix);

        app('cache')->forgetDriver($this->storeName);

        // This is important because the `CacheManager` will have the `$app['config']` array cached
        // with old prefixes on the `cache` instance. Simply calling `forgetDriver` only removes
        // the `$store` but doesn't update the `$app['config']`.
        app()->forgetInstance('cache');

        //This is important because the Cache Repository is using an old version of the CacheManager
        app()->forgetInstance('cache.store');

        // Forget the cache repository in the container
        app()->forgetInstance(Repository::class);

        Cache::clearResolvedInstances();
    }
}
