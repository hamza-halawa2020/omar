<?php

namespace App\DTO;

readonly class DealFilter
{
    public function __construct(
        public bool $withContact = false,
        public bool $withAccount = false,
        public bool $withTasks = false,
    )
    {}
}
