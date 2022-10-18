<?php

namespace MichaelNabil230\MultiTenancy;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MichaelNabil230\MultiTenancy\Commands\MultiTenancyCommand;

class MultiTenancyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-multi-tenancy')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-multi-tenancy_table')
            ->hasCommand(MultiTenancyCommand::class);
    }
}
