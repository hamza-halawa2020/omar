<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'domain', 'umrah_enabled', 'company_type'];
    }

    public function isUmrah(): bool
    {
        return $this->company_type === 'umrah';
    }

    public function allowsPermission(string $permission): bool
    {
        return $this->isUmrah() === str_starts_with($permission, 'umrah_');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
