<?php

namespace MichaelNabil230\MultiTenancy\Tests\Feature\Commands\TestClasses;

use Illuminate\Console\Command;
use MichaelNabil230\MultiTenancy\Commands\Concerns\TenantAware;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class TenantNoopCommand extends Command
{
    use TenantAware;

    protected $name = 'tenant:noop';

    protected $description = 'Execute noop for tenant(s)';

    public function handle()
    {
        $this->components->info('Tenant ID is '.MultiTenancy::current()->id);

        return Command::SUCCESS;
    }
}
