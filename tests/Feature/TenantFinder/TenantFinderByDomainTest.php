<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\TenantFinder;

use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByDomain;
use MichaelNabil230\MultiTenancy\Tests\TestCase;

class TenantFinderByDomainTest extends TestCase
{
    protected TenantFinderByDomain $tenantFinder;

    protected Tenant $tenant;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantFinder = new TenantFinderByDomain();

        $this->tenant = Tenant::factory()->create();
        $this->tenant->domains()->create(['domain' => 'my-domain.com']);
    }

    public function test_can_find_a_tenant_for_the_current_domain()
    {
        $request = Request::create('https://my-domain.com');

        $this->assertEquals($this->tenant->id, $this->tenantFinder->find($request)->id);
    }

    public function test_will_return_null_if_no_tenant_can_be_found_for_the_current_domain()
    {
        $this->expectException(TenantCouldNotBeIdentifiedOnDomainException::class);
        $this->expectExceptionMessage('Tenant could not be identified on domain another-domain.com');

        $request = Request::create('https://another-domain.com');

        $this->assertNull($this->tenantFinder->find($request));
    }
}
