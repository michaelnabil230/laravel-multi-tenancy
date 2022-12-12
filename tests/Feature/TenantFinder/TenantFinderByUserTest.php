<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\TenantFinder;

use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByUser;
use MichaelNabil230\MultiTenancy\Tests\TestCase;

class TenantFinderByUserTest extends TestCase
{
    protected TenantFinderByUser $tenantFinder;

    protected Tenant $tenant;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantFinder = new TenantFinderByUser();

        $this->tenant = Tenant::factory()->create();
        $this->tenant->domains()->create(['domain' => 'my-domain.com']);
    }
}
