<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultiTenancyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-multi-tenancy')
            ->hasConfigFile()
            ->hasMigrations(['create_multi_tenancy_table', 'create_subscription_table'])
            ->publishesServiceProvider('TenancyServiceProvider')
            ->hasInstallCommand(fn (InstallCommand $install) => $this->installCommand($install))
            ->hasCommands([
                Commands\CreateTenant::class,
                Commands\Seed::class,
            ]);
    }

    public function registeringPackage()
    {
        $this
            ->registerBootstrappers()
            ->registerFeatures()
            ->registerTenancyEvents()
            ->registerSubscriptionEvents();
    }

    public function bootingPackage()
    {
        $this
            ->registerPublishing()
            ->makeTenancyMiddlewareHighestPriority()
            ->addTenantBlueprintMacro();
    }

    protected function registerBootstrappers()
    {
        // Make sure bootstrappers are stateful (singletons).
        foreach (config('multi-tenancy.bootstrappers', []) as $bootstrapper) {
            if (method_exists($bootstrapper, '__constructStatic')) {
                $bootstrapper::__constructStatic($this->app);
            }

            $this->app->singleton($bootstrapper);
        }

        return $this;
    }

    protected function registerFeatures()
    {
        foreach (config('multi-tenancy.features', []) as $feature) {
            $this->app[$feature]->bootstrap();
        }

        return $this;
    }

    protected function registerTenancyEvents()
    {
        foreach (config('multi-tenancy.events', []) as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        return $this;
    }

    protected function registerSubscriptionEvents()
    {
        if (! MultiTenancy::subscriptionEnable()) {
            return $this;
        }

        foreach (config('multi-tenancy.subscription.events', []) as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        return $this;
    }

    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/stubs/database/seeders/TenantDatabaseSeeder.php.stub' => database_path('seeders/TenantDatabaseSeeder.php'),
            ], "{$this->package->shortName()}-seeder");

            $this->publishes([
                __DIR__.'/../resources/stubs/routes/web.php.stub' => base_path('routes/tenant/web.php'),
                __DIR__.'/../resources/stubs/routes/api.php.stub' => base_path('routes/tenant/api.php'),
            ], "{$this->package->shortName()}-routes");
        }

        return $this;
    }

    private function installCommand(InstallCommand $install): void
    {
        $install
            ->startWith(function ($self) {
                $this
                    ->publishProvider($self)
                    ->publishRoutes($self);
            })
            ->publishMigrations()
            ->askToRunMigrations()
            ->publishConfigFile()
            ->askToStarRepoOnGitHub('michaelnabil230/laravel-multi-tenancy')
            ->copyAndRegisterServiceProviderInApp();
    }

    private function publishProvider($self)
    {
        $self->comment('Publishing provider file...');

        $self->callSilently('vendor:publish', [
            '--tag' => "{$this->package->shortName()}-provider",
        ]);

        return $this;
    }

    protected function publishRoutes($self)
    {
        $self->comment('Publishing routes files...');

        $self->callSilently('vendor:publish', [
            '--tag' => "{$this->package->shortName()}-routes",
        ]);

        return $this;
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,

            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            app(Kernel::class)->prependToMiddlewarePriority($middleware);
        }

        return $this;
    }

    protected function addTenantBlueprintMacro()
    {
        Blueprint::macro('tenant', function () {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            $this->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });

        Blueprint::macro('dropTenant', function () {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            $this->dropConstrainedForeignId('tenant_id');
        });

        return $this;
    }
}
