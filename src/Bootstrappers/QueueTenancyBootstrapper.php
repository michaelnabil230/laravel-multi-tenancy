<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Testing\Fakes\QueueFake;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class QueueTenancyBootstrapper implements TenancyBootstrapper
{
    /**
     * Don't persist the same tenant across multiple jobs even if they have the same tenant ID.
     *
     * This is useful when you're changing the tenant's state (e.g. properties in the `data` column) and want the next job to initialize tenancy again
     * with the new data. Features like the Tenant Config are only executed when tenancy is initialized, so the re-initialization is needed in some cases.
     */
    public static bool $forceRefresh = false;

    /**
     * The normal constructor is only executed after tenancy is bootstrapped.
     * However, we're registering a hook to initialize tenancy. Therefore,
     * we need to register the hook at service provider execution time.
     */
    public static function __constructStatic(Application $app)
    {
        static::setUpJobListener($app->make(Dispatcher::class), $app->runningUnitTests());
    }

    public function __construct(protected Repository $config, protected QueueManager $queue)
    {
        $this->config = $config;
        $this->queue = $queue;

        $this->setUpPayloadGenerator();
    }

    protected static function setUpJobListener(Dispatcher $dispatcher, bool $runningTests): void
    {
        $previousTenant = null;

        $dispatcher->listen(JobProcessing::class, function ($event) use (&$previousTenant) {
            $previousTenant = tenant();

            static::initializeTenancyForQueue($event->job->payload()['tenant_id'] ?? null);
        });

        $dispatcher->listen(JobRetryRequested::class, function ($event) use (&$previousTenant) {
            $previousTenant = tenant();

            static::initializeTenancyForQueue($event->payload()['tenant_id'] ?? null);
        });

        // If we're running tests, we make sure to clean up after any artisan('queue:work') calls
        $revertToPreviousState = function ($event) use (&$previousTenant, $runningTests) {
            if ($runningTests) {
                static::revertToPreviousState($event, $previousTenant);
            }
        };

        $dispatcher->listen(JobProcessed::class, $revertToPreviousState); // artisan('queue:work') which succeeds
        $dispatcher->listen(JobFailed::class, $revertToPreviousState); // artisan('queue:work') which fails
    }

    protected static function initializeTenancyForQueue($tenantId): void
    {
        if (! $tenantId) {
            // The job is not tenant-aware
            if (MultiTenancy::checkCurrent()) {
                // Tenancy was initialized, so we revert back to the central context
                MultiTenancy::forgetCurrent();
            }

            return;
        }

        if (static::$forceRefresh) {
            // Re-initialize tenancy between all jobs
            if (MultiTenancy::checkCurrent()) {
                MultiTenancy::forgetCurrent();
            }

            Tenant::find($tenantId)->initialize();

            return;
        }

        if (MultiTenancy::checkCurrent()) {
            // Tenancy is already initialized
            if (tenant()->getKey() === $tenantId) {
                // It's initialized for the same tenant (e.g. dispatchNow was used, or the previous job also ran for this tenant)
                return;
            }
        }

        // Tenancy was either not initialized, or initialized for a different tenant.
        // Therefore, we initialize it for the correct tenant.
        Tenant::find($tenantId)->initialize();
    }

    protected static function revertToPreviousState($event, &$previousTenant)
    {
        $tenantId = $event->job->payload()['tenant_id'] ?? null;

        // The job was not tenant-aware
        if (! $tenantId) {
            return;
        }

        // Revert back to the previous tenant
        if (tenant() && $previousTenant && $previousTenant->isNot(tenant())) {
            $previousTenant->initialize();
        }

        // End tenancy
        if (tenant() && (! $previousTenant)) {
            MultiTenancy::forgetCurrent();
        }
    }

    protected function setUpPayloadGenerator()
    {
        $bootstrapper = &$this;

        if (! $this->queue instanceof QueueFake) {
            $this->queue->createPayloadUsing(function ($connection) use (&$bootstrapper) {
                return $bootstrapper->getPayload($connection);
            });
        }
    }

    public function bootstrap(Tenant $tenant)
    {
        //
    }

    public function revert()
    {
        //
    }

    public function getPayload(string $connection)
    {
        if (! MultiTenancy::checkCurrent()) {
            return [];
        }

        if ($this->config["queue.connections.$connection.central"]) {
            return [];
        }

        return [
            'tenant_id' => tenant()->getKey(),
        ];
    }
}
