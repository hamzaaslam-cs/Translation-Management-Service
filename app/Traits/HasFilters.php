<?php

namespace App\Traits;

trait HasFilters
{
    public function scopeFilter($builder, $filters, $used_as = 'object')
    {
        if (! empty($filters)) {
            $filters->used_as = $used_as;

            return $filters->apply($builder);
        }

        return $builder;
    }
}
