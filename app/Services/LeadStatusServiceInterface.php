<?php

namespace App\Services;

use App\DTO\QueryFilters\LeadStatusFilter;
use App\Models\LeadsStatus;
use Illuminate\Support\Collection;

interface LeadStatusServiceInterface
{
    public function getById(int $id): LeadsStatus;

    public function pluck(string $value, string $key = null, LeadStatusFilter $filter = null): Collection;
}
