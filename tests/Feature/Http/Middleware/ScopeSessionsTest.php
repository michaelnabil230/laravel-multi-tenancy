<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\Http\Middleware;

use Illuminate\Support\Facades\Route;
use MichaelNabil230\MultiTenancy\Middleware\ScopeSessions;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ScopeSessionsTest extends TestCase
{
    protected Tenant $tenant;

    public function setUp(): void
    {
        parent::setUp();

        Route::get('test-middleware', fn () => 'ok')->middleware(['web', ScopeSessions::class]);

        /** @var \MichaelNabil230\MultiTenancy\Models\Tenant $tenant */
        $this->tenant = Tenant::factory()->create();
        $this->tenant->initialize();
    }

    public function test_will_set_the_tenant_id_if_it_has_not_been_set()
    {
        $this->assertNull(session('tenant_id'));

        $this
            ->get('test-middleware')
            ->assertOk();

        $this->assertEquals($this->tenant->id, session('ensure_valid_tenant_session_tenant_id'));
    }

    public function test_will_allow_requests_for_the_tenant_set_in_the_session()
    {
        session()->put('ensure_valid_tenant_session_tenant_id', $this->tenant->id);

        $this
            ->get('test-middleware')
            ->assertOk();
    }

    public function test_will_not_allow_requests_for_other_tenants()
    {
        session()->put('ensure_valid_tenant_session_tenant_id', 2);

        $this
            ->get('test-middleware')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
