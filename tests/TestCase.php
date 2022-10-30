<?php

namespace MichaelNabil230\MultiTenancy\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\MultiTenancyServiceProvider;
use MichaelNabil230\MultiTenancy\Tests\Etc\User;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MichaelNabil230\\MultiTenancy\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MultiTenancyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        MultiTenancy::useOwnerModel(User::class);

        $migration = include __DIR__.'/../database/migrations/create_multi_tenancy_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/create_subscription_table.php.stub';
        $migration->up();
    }
}
