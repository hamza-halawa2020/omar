<?php

namespace App\DTO;

readonly class LeadFilter
{
    public function __construct(
        public ?bool   $withAssignedUsers = false,
        public ?bool $available = null,
        public ?bool $converted = null,
    ) {}
}
