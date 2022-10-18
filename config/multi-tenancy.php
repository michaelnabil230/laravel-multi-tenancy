<?php

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
