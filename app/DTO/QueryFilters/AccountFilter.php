<?php

namespace App\DTO\QueryFilters;

readonly class AccountFilter
{
    public function __construct(
        public ?bool $withAssignedUser = null,
        public ?int  $perPage = 10,
    )
    {
    }
}
