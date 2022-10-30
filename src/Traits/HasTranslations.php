<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations as HasTranslationsBase;

trait HasTranslations
{
    use HasTranslationsBase;

    public function scopeWhereTranslation(Builder $query, string $column, mixed $operator = null, mixed $value = null, string $locale = null): Builder
    {
        $this->guardAgainstNonTranslatableAttribute($column);

        $locale = $locale ?? $this->getLocale();

        [$value, $operator] = $query->getQuery()->prepareValueAndOperator(
            $value, $operator, func_num_args() === 3
        );

        return $query->where("{$column}->{$locale}", $operator, $value);
    }
}
