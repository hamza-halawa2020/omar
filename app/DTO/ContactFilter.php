<?php

namespace App\DTO;

readonly class ContactFilter
{
    public function __construct(public readonly ?bool $withAccount = null)
    {
    }
}
