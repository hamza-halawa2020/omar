<?php

namespace App\Repositories\impl;

use App\DTO\LeadFilter;
use App\Models\Lead;
use App\Repositories\LeadRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LeadRepository implements LeadRepositoryInterface
{
    public function getAll(LeadFilter $filter = null): LengthAwarePaginator
    {
        return Lead::query()
            ->when($filter?->available, function ($query) use ($filter) {
                return $query->whereAvailable();
            })
            ->when($filter?->withAssignedUsers, function ($query) use ($filter) {
                return $query->withAssignedUsers();
            })
            ->when($filter?->converted, function ($query) use ($filter) {
                return $query->whereConverted();
            })
            ->paginate($filter->perPage, ['*'], 'leads_page')
            ->appends(preserveOtherPagination('leads_page'));
    }

    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function getById(int $id): Lead
    {
        return Lead::findOrFail($id);
    }

    public function update(Lead $lead, array $data): bool
    {
        return $lead->update($data);
    }

    public function delete(Lead $lead): bool
    {
        return $lead->delete();
    }

    public function pluck(string $value, string $key = null, LeadFilter $filter = null): Collection
    {
        $query = Lead::query()
            ->when($filter?->available, function ($query) use ($filter) {
                return $query->whereAvailable();
            })
            ->when($filter?->withAssignedUsers, function ($query) use ($filter) {
                return $query->withAssignedUsers();
            })
            ->when($filter?->converted, function ($query) use ($filter) {
                return $query->whereConverted();
            });

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }
}
