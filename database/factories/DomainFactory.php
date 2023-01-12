<?php

namespace MichaelNabil230\MultiTenancy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MichaelNabil230\MultiTenancy\Models\Domain;

class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition()
    {
        return [
            'domain' => fake()->domainName(),
            'is_premium' => fake()->boolean(),
            'is_verified' => fake()->boolean(),
        ];
    }
}
