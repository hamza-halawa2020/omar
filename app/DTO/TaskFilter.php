<?php

namespace App\DTO;

readonly class TaskFilter
{
    public function __construct(
        public ?bool $withRelatedTo = null,
        public ?bool $withAssignedUser = null,
        public ?int  $perPage = 10,
    )
    {
    }
}
