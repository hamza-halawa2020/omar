<?php

namespace App\Traits\Repositories;

use App\DTO\QueryFilters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function applyFilters(Builder $query, ?QueryFilter $filter): Builder
    {
        if (!$filter) return $query;

        foreach ($filter->filters() as $property => $callback) {
            if (property_exists($filter, $property) && $filter->$property) {
                $callback($query);
            }
        }

        return $query;
    }
}
