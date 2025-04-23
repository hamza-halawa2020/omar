<?php

namespace App\DTO\QueryFilters;

abstract class QueryFilter
{
    abstract public function filters(): array;

}
