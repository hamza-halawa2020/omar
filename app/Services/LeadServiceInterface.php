<?php

namespace App\Services;

use App\DTO\LeadFilter;
use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LeadServiceInterface
{
    public function getAll(LeadFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Lead;

    public function getOne(int $id): Lead;

    public function update(Lead $lead, array $data): bool;

    public function delete(Lead $lead): bool;

    public function pluck(string $value, string $key = null): Collection;

    public function convertIntoAccountAndContact(Lead $lead): bool;
}
