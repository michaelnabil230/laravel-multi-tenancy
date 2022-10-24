<?php

namespace MichaelNabil230\MultiTenancy\Models\Concerns;

use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

trait HasScopedValidationRules
{
    public function unique($table, $column = 'NULL')
    {
        return (new Unique($table, $column))->where('tenant_id', $this->getKey());
    }

    public function exists($table, $column = 'NULL')
    {
        return (new Exists($table, $column))->where('tenant_id', $this->getKey());
    }
}
