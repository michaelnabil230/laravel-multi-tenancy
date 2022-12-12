<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\TenantFinder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use MichaelNabil230\MultiTenancy\Exceptions\TenantCouldNotBeIdentifiedByRequestDataException;
use MichaelNabil230\MultiTenancy\Middleware\InitializeTenancyByRequestData;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByRequest;
use MichaelNabil230\MultiTenancy\Tests\TestCase;

class TenantFinderByRequestTest extends TestCase
{
    protected TenantFinderByRequest $tenantFinder;

    protected Tenant $tenant;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantFinder = new TenantFinderByRequest();

        $this->tenant = Tenant::factory()->create();
    }

    public function test_can_find_a_tenant_for_the_current_header()
    {
        $request = Request::create('https://my-domain.com');
        $request->headers->set(TenantFinderByRequest::$header, $this->tenant->id);

        $this->assertEquals($this->tenant->id, $this->tenantFinder->find($request)->id);
    }

    public function test_will_return_null_if_no_tenant_can_be_found_for_the_current_header()
    {
        $this->expectException(TenantCouldNotBeIdentifiedByRequestDataException::class);
        $this->expectExceptionMessage('Tenant could not be identified by request data with payload: 1');

        $request = Request::create('https://my-domain.com');
        $request->headers->set(TenantFinderByRequest::$header, 1);

        $this->assertNull($this->tenantFinder->find($request));
    }

    public function test_can_find_a_tenant_for_the_current_query_parameter()
    {
        Route::middleware(InitializeTenancyByRequestData::class)->get('/', function () {
            return 'Tenant id: '.tenant('id');
        });

        $this
            ->get('/?tenant='.$this->tenant->id)
            ->assertSuccessful()
            ->assertSeeText('Tenant id: '.$this->tenant->id);
    }

    public function test_will_return_null_if_no_tenant_can_be_found_for_the_query_parameter()
    {
        $this->expectException(TenantCouldNotBeIdentifiedByRequestDataException::class);
        $this->expectExceptionMessage('Tenant could not be identified by request data with payload: 1');

        Route::middleware(InitializeTenancyByRequestData::class)->get('/', function () {
            return 'Tenant id: '.tenant('id');
        });

        $this
            ->withoutExceptionHandling()
            ->get('/?tenant=1')
            ->assertDontSeeText('Tenant id: '.$this->tenant->id);
    }

    public function test_can_find_a_tenant_for_the_current_parameter_name()
    {
        $tenantParameterName = TenantFinderByRequest::$tenantParameterName;

        Route::middleware(InitializeTenancyByRequestData::class)->get("/{{$tenantParameterName}}", function () {
            return 'Tenant id: '.tenant('id');
        });

        $this
            ->get('/'.$this->tenant->id)
            ->assertSuccessful()
            ->assertSeeText('Tenant id: '.$this->tenant->id);
    }

    public function test_will_return_null_if_no_tenant_can_be_found_for_the_current_parameter_name()
    {
        $this->expectException(TenantCouldNotBeIdentifiedByPathException::class);
        $this->expectExceptionMessage('Tenant could not be identified on path with tenant_id: 1');

        $tenantParameterName = TenantFinderByRequest::$tenantParameterName;

        Route::middleware(InitializeTenancyByRequestData::class)->get("/{{$tenantParameterName}}", function () {
            return 'Tenant id: '.tenant('id');
        });

        $this
            ->withoutExceptionHandling()
            ->get('/1')
            ->assertDontSeeText('Tenant id: '.$this->tenant->id);
    }
}
