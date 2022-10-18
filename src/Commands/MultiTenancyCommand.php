<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Console\Command;

class MultiTenancyCommand extends Command
{
    public $signature = 'laravel-multi-tenancy';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
