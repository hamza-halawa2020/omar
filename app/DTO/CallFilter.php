<?php

namespace App\DTO;

readonly class CallFilter
{
    public function __construct(
        public ?int $perPage = 10,
    )
    {
    }
}
