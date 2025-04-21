<?php

namespace App\DTO\QueryFilters;

readonly class LeadStatusFilter
{
    public function __construct(public readonly ?int $parentId = null)
    {
    }
}
