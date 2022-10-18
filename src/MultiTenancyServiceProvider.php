<?php

namespace MichaelNabil230\MultiTenancy;

use MichaelNabil230\MultiTenancy\Commands\MultiTenancyCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
