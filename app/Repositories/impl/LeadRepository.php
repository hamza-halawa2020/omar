<?php

namespace App\Repositories\impl;

use App\DTO\LeadFilter;
use App\Models\Lead;
use App\Repositories\LeadRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadRepository implements LeadRepositoryInterface
{
    public function getAll(LeadFilter $filter): LengthAwarePaginator
    {
        return Lead::query()
            ->when($filter->available, function ($query) use ($filter) {
                return $query->whereAvailable();
            })
            ->when($filter->withAssignedUsers, function ($query) use ($filter) {
                return $query->withAssignedUsers();
            })
            ->when($filter->converted, function ($query) use ($filter) {
                return $query->whereConverted();
            })->paginate();
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
}
