<?php

namespace App\DTO\QueryFilters;

readonly class DealFilter
{
    public function __construct(
        public bool $withContact = false,
        public bool $withAccount = false,
        public bool $withTasks = false,
        public ?int $accountId = null,
        public ?int $contactId = null,
        public ?int $perPage = 10,
    )
    {
    }
}
