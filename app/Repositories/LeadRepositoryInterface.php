<?php

namespace App\Repositories;

use App\DTO\LeadFilter;
use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    public function getAll(LeadFilter $filter): LengthAwarePaginator;

    public function create(array $data): Lead;

    public function getById(int $id): Lead;

    public function update(Lead $lead, array $data): bool;

    public function delete(Lead $lead): bool;
}
