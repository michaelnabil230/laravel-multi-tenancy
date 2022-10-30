# Tenancy for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/michaelnabil230/laravel-multi-tenancy.svg?style=flat-square)](https://packagist.org/packages/michaelnabil230/laravel-multi-tenancy)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/michaelnabil230/laravel-multi-tenancy/run-tests?label=tests)](https://github.com/michaelnabil230/laravel-multi-tenancy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/michaelnabil230/laravel-multi-tenancy/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/michaelnabil230/laravel-multi-tenancy/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/michaelnabil230/laravel-multi-tenancy.svg?style=flat-square)](https://packagist.org/packages/michaelnabil230/laravel-multi-tenancy)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require michaelnabil230/laravel-multi-tenancy
```

You can publish and run the migrations with:

```bash
php artisan multi-tenancy:install
```

This is the contents of the published config file:

```php

use MichaelNabil230\MultiTenancy\Events;
use MichaelNabil230\MultiTenancy\Listeners;

return [

    /**
     * NameServer of the server for ex:'ns1.contabo.net'.
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
     * Tenancy bootstrappers are executed when the tenancy is initialized.
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
     * Note: You don't need to use this if you're using Redis only for the cache.
     * Redis tenancy is only relevant if you're making direct Redis calls,
     * either using the Redis facade or injecting it as a dependency.
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
     * Each key in the cache will have a tag applied to it. This tag is used to
     * scope the cache both when writing to it and when reading from it.
     *
     * You can clear the cache selectively by specifying the tag.
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
     * not needed for the tenancy to be bootstrapped. They are run
     * regardless of whether the tenancy has been initialized.
     *
     * See the documentation page for each class to
     * understand which ones you want to enable.
     */
    'features' => [
        // MichaelNabil230\MultiTenancy\Features\TelescopeTags::class,
        // MichaelNabil230\MultiTenancy\Features\TenantConfig::class,
        // MichaelNabil230\MultiTenancy\Features\TenantSetting::class,
    ],

    /**
     * Parameters used by the db:seed command.
     */
    'seeder_parameters' => [
        '--class' => 'TenantDatabaseSeeder',
        '--force' => true,
    ],

    /**
     * Subscription for Tenant
     */
    'subscription' => [
        /**
         * Enable if you need tenant has subscription in your application
         */
        'enable' => false,

        /**
         * Route for the subscription index
         */
        // 'route' => route('subscription.index'),

        /**
         * All events for subscription
         */
        'events' => [
            // Subscription events
            Events\Subscription\SubscriptionCreated::class => [],
            Events\Subscription\SubscriptionUpdated::class => [],
            Events\Subscription\SubscriptionCancelled::class => [],
            Events\Subscription\SubscriptionChangePlan::class => [],
            Events\Subscription\SubscriptionRenewed::class => [],
            Events\Subscription\SubscriptionResume::class => [],
        ],
    ],
];
```

## Usage

```php

```

## Support

[![](.assets/ko-fi.png)](https://ko-fi.com/michaelnabil230)[![](.assets/buymeacoffee.png)](https://www.buymeacoffee.com/michaelnabil230)[![](.assets/paypal.png)](https://www.paypal.com/paypalme/MichaelNabil23)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Nabil](https://github.com/MichaelNabil230)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
