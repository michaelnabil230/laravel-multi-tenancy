<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

trait HasValidationRules
{
    /**
     * Get a unique with the wrapper tenant constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \Illuminate\Validation\Rules\Unique
     */
    public function unique(string $table, string $column = 'NULL'): Unique
    {
        return Rule::unique($table, $column)->where('tenant_id', $this->getKey());
    }

    /**
     * Get an exists with the wrapper tenant constraint builder instance
     *
     * @param  string  $table
     * @param  string  $column
     * @return \Illuminate\Validation\Rules\Exists
     */
    public function exists(string $table, string $column = 'NULL'): Exists
    {
        return Rule::exists($table, $column)->where('tenant_id', $this->getKey());
    }
}
