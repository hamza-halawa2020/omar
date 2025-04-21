<?php

namespace App\Services;

use App\DTO\QueryFilters\ContactFilter;
use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ContactServiceInterface
{
    public function getAll(ContactFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Contact;

    public function getOne(int $id): Contact;

    public function update(Contact $contact, array $data): bool;

    public function delete(Contact $contact): bool;

    public function pluck(string $value, string $key = null, ContactFilter $filter = null): Collection;
}
