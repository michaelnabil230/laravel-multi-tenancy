<?php

namespace MichaelNabil230\MultiTenancy\Commands\Concerns;

use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Input\InputOption;

trait HasATenantsOption
{
    protected function getOptions()
    {
        return array_merge([
            ['tenants', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'All ids for tenants', null],
        ], parent::getOptions());
    }

    protected function getTenants(): LazyCollection
    {
        return tenant()
            ->query()
            ->when($this->option('tenants'), function ($query, $tenants) {
                $query->whereIn(tenant()->getKey(), $tenants);
            })
            ->cursor();
    }

    public function __construct()
    {
        parent::__construct();

        $this->specifyParameters();
    }
}
