<?php

namespace App\DTO;

readonly class AccountFilter
{
    public function __construct(
        public ?bool         $withAssignedUser = null,
        public readonly ?int $perPage = 10,
    )
    {
    }
}
