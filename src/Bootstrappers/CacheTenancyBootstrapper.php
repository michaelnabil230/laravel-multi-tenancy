<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\CacheManager as TenantCacheManager;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class CacheTenancyBootstrapper implements TenancyBootstrapper
{
    protected CacheManager|null $originalCache;

    public function __construct(
        protected Application $app,
    ) {
    }

    public function bootstrap(Tenant $tenant)
    {
        $this->resetFacadeCache();

        $this->originalCache = $this->originalCache ?? $this->app['cache'];
        $this->app->extend('cache', fn () => new TenantCacheManager($this->app));
    }

    public function revert()
    {
        $this->resetFacadeCache();

        $this->app->extend('cache', fn () => $this->originalCache);

        $this->originalCache = null;
    }

    /**
     * This wouldn't be necessary, but is needed when a call to the
     * facade has been made prior to bootstrapping tenancy. The
     * facade has its own cache, separate from the container.
     */
    public function resetFacadeCache()
    {
        Cache::clearResolvedInstances();
    }
}
