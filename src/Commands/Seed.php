<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Console\Seeds\SeedCommand;
use MichaelNabil230\MultiTenancy\Commands\Concerns\HasATenantsOption;
use MichaelNabil230\MultiTenancy\Events\DataBase;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'tenants:seed', description: 'Seed tenant database(s).')]
class Seed extends SeedCommand
{
    use HasATenantsOption;

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
    protected $description = 'Seed tenant database(s).';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ConnectionResolverInterface $resolver)
    {
        parent::__construct($resolver);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (config('multi-tenancy.seeder_parameters') as $parameter => $value) {
            if (! $this->input->hasParameterOption($parameter)) {
                $this->input->setOption(ltrim($parameter, '-'), $value);
            }
        }

        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->components->info('Running seed tenants');

        tenancy()->runForMultiple($this->option('tenants'), function ($tenant) {
            $this->components->task($tenant->getKey(), function () use ($tenant) {
                event(new DataBase\SeedingDatabase($tenant));

                // Seed
                $result = parent::handle();

                event(new DataBase\DatabaseSeeded($tenant));

                return $result;
            });
        });

        return 0;
    }
}
