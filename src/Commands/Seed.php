<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\MultiTenancy\Commands\Concerns\TenantAware;
use MichaelNabil230\MultiTenancy\Events\DataBase;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'tenants:seed', description: 'Seed tenant in database.')]
class Seed extends Command
{
    use TenantAware;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenants:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed tenant in database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arguments = config('multi-tenancy.seeder_parameters');

        if (is_null($arguments)) {
            $this->components->error('Please fill the seeder in the config file');

            return 1;
        }

        $this->components->info('Running seed for tenants');

        $tenant = MultiTenancy::current();

        $this->components->task('Tenant: '.$tenant->getKey(), function () use ($tenant, $arguments) {
            event(new DataBase\SeedingDatabase($tenant));

            // Seed
            $result = $this->callSilent('db:seed', $arguments);

            event(new DataBase\DatabaseSeeded($tenant));

            return $result;
        });

        return 0;
    }
}
