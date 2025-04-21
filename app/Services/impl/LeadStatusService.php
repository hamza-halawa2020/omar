<?php

namespace App\Services\impl;

use App\DTO\QueryFilters\LeadStatusFilter;
use App\Models\LeadsStatus;
use App\Repositories\LeadStatusRepositoryInterface;
use App\Services\LeadStatusServiceInterface;
use Illuminate\Support\Collection;

readonly class LeadStatusService implements LeadStatusServiceInterface
{
    public function __construct(private LeadStatusRepositoryInterface $leadStatusRepository)
    {
    }

    public function getById(int $id): LeadsStatus
    {
        return $this->leadStatusRepository->getById($id);
    }

    public function pluck(string $value, string $key = null, LeadStatusFilter $filter = null): Collection
    {
        return $this->leadStatusRepository->pluck($value, $key, $filter);
    }
}
