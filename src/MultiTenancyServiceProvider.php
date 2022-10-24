<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Event;
use MichaelNabil230\MultiTenancy\Models\Tenant;
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
            ->hasMigration('create_multi_tenancy_table')
            ->publishesServiceProvider('TenancyServiceProvider')
            ->hasInstallCommand(fn (InstallCommand $install) => $this->installCommand($install))
            ->hasCommands([
                Commands\CreateTenant::class,
            ]);
    }

    public function bootingPackage()
    {
        $this->registerPublishing();
        $this->addTenantBlueprintMacro();
    }

    public function registeringPackage()
    {
        // Make sure Tenancy is stateful.
        $this->app->singleton(Tenancy::class);

        // Make sure features are bootstrapped as soon as Tenancy is instantiated.
        $this->app->extend(Tenancy::class, function (Tenancy $tenancy) {
            foreach ($this->app['config']['multi-tenancy.features'] ?? [] as $feature) {
                $this->app[$feature]->bootstrap($tenancy);
            }

            return $tenancy;
        });

        // Make it possible to inject the current tenant by trephining the Tenant contract.
        $this->app->bind(Tenant::class, fn ($app) => $app[Tenancy::class]->tenant);

        // Make sure bootstrappers are stateful (singletons).
        foreach ($this->app['config']['multi-tenancy.bootstrappers'] ?? [] as $bootstrapper) {
            if (method_exists($bootstrapper, '__constructStatic')) {
                $bootstrapper::__constructStatic($this->app);
            }

            $this->app->singleton($bootstrapper);
        }

        foreach ($this->app['config']['multi-tenancy.events'] ?? [] as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/stubs/routes/web.php.stub' => base_path('routes/tenant/web.php'),
                __DIR__.'/../resources/stubs/routes/api.php.stub' => base_path('routes/tenant/api.php'),
            ], "{$this->package->shortName()}-routes");
        }
    }

    private function installCommand(InstallCommand $install): void
    {
        $install
            ->startWith(function ($self) {
                $this->publishProvider($self);
                $this->publishRoutes($self);
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
            '--tag' => "{$self->package->shortName()}-provider",
        ]);
    }

    private function publishRoutes($self)
    {
        $self->comment('Publishing routes files...');

        $self->callSilently('vendor:publish', [
            '--tag' => "{$self->package->shortName()}-routes",
        ]);
    }

    private function addTenantBlueprintMacro()
    {
        Blueprint::macro('tenant', function () {
            $this->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });

        Blueprint::macro('dropTenant', function () {
            $this->dropConstrainedForeignId('tenant_id');
        });
    }
}
