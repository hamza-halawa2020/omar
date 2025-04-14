<?php

namespace App\Services;

use App\DTO\LeadFilter;
use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadServiceInterface
{
    public function getAll(LeadFilter $filter): LengthAwarePaginator;

    public function create(array $data): Lead;

    public function getOne(int $id): Lead;

    public function update(Lead $lead, array $data): bool;

    public function delete(Lead $lead): bool;

    public function convertIntoAccountAndContact(Lead $lead): bool;
}
