<?php

namespace MichaelNabil230\MultiTenancy;

use MichaelNabil230\MultiTenancy\Commands\MultiTenancyCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultiTenancyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-multi-tenancy')
            ->hasConfigFile()
            ->hasMigration('create_laravel-multi-tenancy_table')
            ->publishesServiceProvider('MultiTenancyServiceProvider')
            ->hasInstallCommand(fn ($self) => $this->installCommand($self))
            ->hasCommands([
                Commands\CreateTenant::class,
            ]);
    }

    public function bootingPackage()
    {
        $this->registerPublishing();
    }

    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/TenancyServiceProvider.stub' => app_path('Providers/TenancyServiceProvider.php'),
            ], "{$this->package->shortName()}-provider");

            $this->publishes([
                __DIR__.'/../stubs/routes/web.stub' => base_path('routes/tenant/web.php'),
                __DIR__.'/../stubs/routes/api.stub' => base_path('routes/tenant/api.php'),
            ], "{$this->package->shortName()}-routes");
        }
    }

    private function installCommand($self): void
    {
        $self
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

    public function publishProvider($self)
    {
        $self->comment('Publishing provider file...');

        $self->callSilently('vendor:publish', [
            '--tag' => "{$self->package->shortName()}-provider",
        ]);
    }

    public function publishRoutes($self)
    {
        $self->comment('Publishing routes files...');

        $self->callSilently('vendor:publish', [
            '--tag' => "{$self->package->shortName()}-routes",
        ]);
    }
}
