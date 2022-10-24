<?php

use App\Models\User as Owner;
use MichaelNabil230\MultiTenancy\Events;
use MichaelNabil230\MultiTenancy\Listeners;
use MichaelNabil230\MultiTenancy\Models\Domain;
use MichaelNabil230\MultiTenancy\Models\Tenant;

return [

    /**
     * NameServer of server for ex:'ns1.contabo.net'.
     */
    'name_server' => null,

    /**
     * The list of domains hosting your central app.
     *
     * Only relevant if you're using the domain or subdomain identification middleware.
     */
    'central_domains' => [
        '127.0.0.1',
        'localhost',
    ],

    /**
     * All events for tenancy
     */
    'events' => [
        // Tenant events
        Events\Tenant\CreatingTenant::class => [],
        Events\Tenant\TenantCreated::class => [
            Listeners\SeedDatabase::class,
        ],
        Events\Tenant\SavingTenant::class => [],
        Events\Tenant\TenantSaved::class => [],
        Events\Tenant\UpdatingTenant::class => [],
        Events\Tenant\TenantUpdated::class => [],
        Events\Tenant\DeletingTenant::class => [],
        Events\Tenant\TenantDeleted::class => [],

        // Domain events
        Events\Domain\CreatingDomain::class => [],
        Events\Domain\DomainCreated::class => [],
        Events\Domain\SavingDomain::class => [],
        Events\Domain\DomainSaved::class => [],
        Events\Domain\UpdatingDomain::class => [],
        Events\Domain\DomainUpdated::class => [],
        Events\Domain\DeletingDomain::class => [],
        Events\Domain\DomainDeleted::class => [],

        // Tenancy events
        Events\InitializingTenancy::class => [],
        Events\TenancyInitialized::class => [
            Listeners\BootstrapTenancy::class,
        ],
        Events\EndingTenancy::class => [],
        Events\TenancyEnded::class => [],
        Events\BootstrappingTenancy::class => [],
        Events\TenancyBootstrapped::class => [],
    ],

    /**
     * Tenancy bootstrappers are executed when tenancy is initialized.
     * Their responsibility is making Laravel features tenant-aware.
     *
     * To configure their behavior, see the config keys below.
     */
    'bootstrappers' => [
        MichaelNabil230\MultiTenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        MichaelNabil230\MultiTenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        MichaelNabil230\MultiTenancy\Bootstrappers\QueueTenancyBootstrapper::class,
        // MichaelNabil230\MultiTenancy\Bootstrappers\RedisTenancyBootstrapper::class, // Note: phpredis is needed
    ],

    /**
     * Redis tenancy config. Used by RedisTenancyBootstrapper.
     *
     * Note: You need phpredis to use Redis tenancy.
     *
     * Note: You don't need to use this if you're using Redis only for cache.
     * Redis tenancy is only relevant if you're making direct Redis calls,
     * either using the Redis facade or by injecting it as a dependency.
     */
    'redis' => [
        'prefix_base' => 'tenant', // Each key in Redis will be prepended by this prefix_base, followed by the tenant id.
        'prefixed_connections' => [ // Redis connections whose keys are prefixed, to separate one tenant's keys from another.
            // 'default',
        ],
    ],

    /**
     * Cache tenancy config. Used by CacheTenancyBootstrapper.
     *
     * This works for all Cache facade calls, cache() helper
     * calls and direct calls to injected cache stores.
     *
     * Each key in cache will have a tag applied on it. This tag is used to
     * scope the cache both when writing to it and when reading from it.
     *
     * You can clear cache selectively by specifying the tag.
     */
    'cache' => [
        'tag_base' => 'tenant', // This tag_base, followed by the tenant_id, will form a tag that will be applied on each cache call.
    ],

    /**
     * Filesystem tenancy config. Used by FilesystemTenancyBootstrapper.
     */
    'filesystem' => [
        /**
         * Each disk listed in the 'disks' array will be suffixed by the suffix_base, followed by the tenant_id.
         */
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
            // 's3',
        ],

        /**
         * Use this for local disks.
         */
        'root_override' => [
            // Disks whose roots should be override after storage_path() is suffixed.
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],

        /**
         * Should storage_path() be suffixed.
         *
         * Note: Disabling this will likely break local disk tenancy. Only disable this if you're using an external file storage service like S3.
         *
         * For the vast majority of applications, this feature should be enabled. But in some
         * edge cases, it can cause issues (like using Passport with Vapor - see #196), so
         * you may want to disable this if you are experiencing these edge case issues.
         */
        'suffix_storage_path' => true,

        /**
         * By default, asset() calls are made multi-tenant too. You can use mix()
         * for global, non-tenant-specific assets. However, you might have some issues when using
         * packages that use asset() calls inside the tenant app. To avoid such issues, you can
         * disable asset() helper tenancy and explicitly use tenant_asset() calls in places
         * where you want to use tenant-specific assets (product images, avatars, etc).
         */
        'asset_helper_tenancy' => true,
    ],

    /**
     * Features are classes that provide additional functionality
     * not needed for tenancy to be bootstrapped. They are run
     * regardless of whether tenancy has been initialized.
     *
     * See the documentation page for each class to
     * understand which ones you want to enable.
     */
    'features' => [
        // MichaelNabil230\MultiTenancy\Features\TelescopeTags::class,
        // MichaelNabil230\MultiTenancy\Features\TenantConfig::class,
    ],

    /**
     * Parameters used by the db:seed command.
     */
    'seeder_parameters' => [
        '--class' => MichaelNabil230\MultiTenancy\Database\Seeders\TenantDatabaseSeeder::class,
        '--force' => true,
    ],

    /**
     * Model of user owner tenant
     */
    'owner_model' => Owner::class,

    /**
     * Model of Tenant
     */
    'tenant_model' => Tenant::class,

    /**
     * Model of Domain
     */
    'domain_model' => Domain::class,
];
