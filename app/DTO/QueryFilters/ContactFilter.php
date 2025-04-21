<?php

namespace App\DTO\QueryFilters;

readonly class ContactFilter
{
    public function __construct(
        public ?bool $withAccount = null,
        public ?int  $accountId = null,
        public ?int  $perPage = 10,
    )
    {
    }
}
