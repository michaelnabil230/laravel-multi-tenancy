<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use MichaelNabil230\MultiTenancy\Commands\Concerns\TenantAware;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Termwind\Terminal;

#[AsCommand(name: 'tenants:artisan', description: 'Artisan commands for tenants.')]
class TenantsArtisanCommand extends Command
{
    use TenantAware;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenants:artisan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan commands for tenants.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (! $artisanCommand = $this->argument('artisanCommand')) {
            $artisanCommand = $this->components->confirm('Which artisan command do you want to run for all tenants?');
        }

        $artisanCommand = addslashes($artisanCommand);

        $tenant = MultiTenancy::current();

        $this->newLine();
        $this->components->twoColumnDetail('<fg=green;options=bold>Running command for tenant:</>', $tenant->getKey());

        $terminalWidth = (new Terminal)->width() / 2;
        $dots = str_repeat('.', max($terminalWidth, 0));
        $this->newLine();

        $this->line($dots);
        $this->newLine();

        Artisan::call($artisanCommand, [], $this->output);

        $this->line($dots);
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge([
            ['artisanCommand', null, InputArgument::REQUIRED, 'The artisan command need'],
        ], parent::getArguments());
    }
}
