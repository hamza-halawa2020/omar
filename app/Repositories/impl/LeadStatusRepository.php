<?php

namespace App\Repositories\impl;

use App\DTO\LeadStatusFilter;
use App\Models\LeadsStatus;
use App\Repositories\LeadStatusRepositoryInterface;
use Illuminate\Support\Collection;

class LeadStatusRepository implements LeadStatusRepositoryInterface
{
    public function getById(int $id): LeadsStatus
    {
        return LeadsStatus::findOrFail($id);
    }

    public function pluck(string $value, string $key = null, LeadStatusFilter $filter = null): Collection
    {
        $query = LeadsStatus::query()
            ->when($filter?->parentId, function ($query) use ($filter) {
                return $query->whereParentStatusId($filter->parentId);
            });

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }
}
