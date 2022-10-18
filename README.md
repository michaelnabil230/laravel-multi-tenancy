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

use App\Model\User as Owner;
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
     * Features are classes that provide additional functionality
     * not needed for tenancy to be bootstrapped. They are run
     * regardless of whether tenancy has been initialized.
     *
     * See the documentation page for each class to
     * understand which ones you want to enable.
     */
    'features' => [
        MichaelNabil230\MultiTenancy\Features\TelescopeTags::class,
        MichaelNabil230\MultiTenancy\Features\TenantConfig::class,
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
