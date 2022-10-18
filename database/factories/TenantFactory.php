<?php

namespace MichaelNabil230\MultiTenancy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition()
    {
        return [];
    }
}
