<?php

namespace App\DTO\QueryFilters;

readonly class CallFilter
{
    public function __construct(
        public ?int $perPage = 10,
    )
    {
    }
}
