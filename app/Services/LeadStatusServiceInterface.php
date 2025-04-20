<?php

namespace App\Services;

use App\DTO\AccountFilter;
use App\DTO\LeadStatusFilter;
use App\Models\Account;
use App\Models\Lead;
use App\Models\LeadsStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LeadStatusServiceInterface
{
    public function getById(int $id): LeadsStatus;

    public function pluck(string $value, string $key = null, LeadStatusFilter $filter = null): Collection;
}
