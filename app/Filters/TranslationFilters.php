<?php

namespace App\Filters;

class TranslationFilters extends QueryFilterBase
{
    public function locale($term)
    {
        return $this->builder->where('locale', $term);
    }

    public function tags($term)
    {
        return $this->builder->whereJsonContains('tags', $term);
    }

    public function key($term)
    {
        return $this->builder->where('key', 'LIKE', '%'.$term.'%');
    }

    public function content($term)
    {
        return $this->builder->where('content', 'LIKE', '%'.$term.'%');
    }
}
