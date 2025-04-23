<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

interface FilterableRepository
{
    public function applyFilters(Builder $query, ?QueryFilter $filter): Builder;
}
