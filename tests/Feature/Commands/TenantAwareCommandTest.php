<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\Commands;

use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\Tests\TestCase;

class TenantAwareCommandTest extends TestCase
{
    protected Tenant $tenant;

    protected Tenant $anotherTenant;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->tenant->initialize();

        $this->anotherTenant = Tenant::factory()->create();
        $this->anotherTenant->initialize();

        MultiTenancy::forgetCurrent();
    }

    public function test_fails_with_a_not_existent_tenant()
    {
        $this
            ->artisan('tenant:noop --tenant=1000')
            ->assertExitCode(1)
            ->expectsOutputToContain('No tenant(s) found.');
    }

    public function test_prints_the_right_tenant()
    {
        $this
            ->artisan('tenant:noop --tenant='.$this->tenant->id)
            ->assertExitCode(0)
            ->expectsOutputToContain('Tenant ID is '.$this->tenant->id);
    }

    public function test_works_with_no_tenant_parameters()
    {
        $this
            ->artisan('tenant:noop')
            ->assertExitCode(0)
            ->expectsOutputToContain('Tenant ID is '.$this->tenant->id)
            ->expectsOutputToContain('Tenant ID is '.$this->anotherTenant->id);
    }
}
