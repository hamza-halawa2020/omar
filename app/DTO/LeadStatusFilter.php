<?php

namespace App\DTO;

readonly class LeadStatusFilter
{
    public function __construct(public readonly ?int $parentId = null)
    {
    }
}
