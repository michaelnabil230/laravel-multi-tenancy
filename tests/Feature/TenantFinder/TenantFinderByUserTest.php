<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\TenantFinder;

use Exception;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MichaelNabil230\MultiTenancy\Middleware\InitializeTenancyByTenantUser;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByUser;
use MichaelNabil230\MultiTenancy\Tests\TestCase;
use MichaelNabil230\MultiTenancy\Tests\TestClasses\User;

class TenantFinderByUserTest extends TestCase
{
    protected TenantFinderByUser $tenantFinder;

    protected Tenant $tenant;

    protected User $user;

    protected User $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantFinder = new TenantFinderByUser();

        $this->tenant = Tenant::factory()->create();

        $this->user = $this->tenant->owner;

        $this->user2 = User::factory()->create();

        Route::middleware([InitializeTenancyByTenantUser::class, Authenticate::class])->get('/', function () {
            return 'Tenant id: '.tenant('id');
        });
    }

    public function test_can_find_a_tenant_for_the_current_user()
    {
        $this
            ->actingAs($this->user)
            ->get('/')
            ->assertSuccessful()
            ->assertSeeText('Tenant id: '.$this->tenant->id);

        $request = Request::create('https://my-domain.com')->setUserResolver(fn () => $this->user);

        $this->assertEquals($this->tenant->id, $this->tenantFinder->find($request)->id);
    }

    public function test_will_return_null_if_no_tenant_can_be_found_for_the_current_user()
    {
        $this->expectExceptionObject(new Exception('The user is not an instance of by `Authenticatable`'));

        $request = Request::create('https://my-domain.com')->setUserResolver(fn () => 'user');

        $this->assertNull($this->tenantFinder->find($request));

        $this
            ->get('/')
            ->assertForbidden()
            ->assertDontSeeText('Tenant id: '.$this->tenant->id);
    }
}
