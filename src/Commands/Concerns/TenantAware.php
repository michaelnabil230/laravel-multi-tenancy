<?php

namespace MichaelNabil230\MultiTenancy\Commands\Concerns;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

trait TenantAware
{
    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenants = Arr::wrap($this->option('tenant'));

        $tenantQuery = MultiTenancy::tenant()::query()
            ->when(! blank($tenants), function ($query) use ($tenants) {
                collect($this->getTenantArtisanSearchFields())
                    ->each(fn ($field) => $query->orWhereIn($field, $tenants));
            });

        if ($tenantQuery->count() === 0) {
            $this->components->error('No tenant(s) found.');

            return Command::FAILURE;
        }

        return $tenantQuery
            ->cursor()
            ->map(fn (Tenant $tenant) => $tenant->execute(fn () => (int) $this->laravel->call([$this, 'handle'])))
            ->sum();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return array_merge([
            ['tenant', 't', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'All ids for tenants'],
        ], parent::getOptions());
    }

    /**
     * The fields for making a search in the query.
     *
     * @return array
     */
    public function getTenantArtisanSearchFields(): array
    {
        return Arr::wrap(config('multi-tenancy.artisan_search_fields', ['id']));
    }
}
