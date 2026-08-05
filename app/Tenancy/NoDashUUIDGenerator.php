<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\UniqueIdentifierGenerator;

class NoDashUUIDGenerator implements UniqueIdentifierGenerator
{
    public static function generate($tenant): string
    {
        return str_replace('-', '', (string) \Illuminate\Support\Str::uuid());
    }
}
