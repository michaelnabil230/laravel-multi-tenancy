<?php

namespace MichaelNabil230\MultiTenancy\Tests;

use Illuminate\Console\Application;
use Illuminate\Database\Eloquent\Factories\Factory;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\MultiTenancyServiceProvider;
use MichaelNabil230\MultiTenancy\Tests\Feature\Commands\TestClasses\TenantNoopCommand;
use MichaelNabil230\MultiTenancy\Tests\TestClasses\User;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithLaravelMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MichaelNabil230\\MultiTenancy\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        $this->bootCommands();

        return [
            MultiTenancyServiceProvider::class,
        ];
    }

    protected function bootCommands(): self
    {
        Application::starting(function ($app) {
            $app->resolveCommands([
                TenantNoopCommand::class,
            ]);
        });

        return $this;
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('queue.default', 'database');
        config()->set('queue.connections.database', [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'connection' => 'testing',
        ]);

        MultiTenancy::useOwnerModel(User::class);

        $migration = include __DIR__.'/../database/migrations/create_multi_tenancy_table.php.stub';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/create_subscription_table.php.stub';
        $migration->up();
    }
}
