<?php

namespace App\DTO\QueryFilters;

readonly class LeadFilter
{
    public function __construct(
        public ?bool $withAssignedUsers = false,
        public ?bool $available = false,
        public ?bool $converted = false,
        public ?bool $withStatus = false,
        public ?int $perPage = 10,
    )
    {
    }
}
