<?php

namespace App\Services\impl;

use App\DTO\QueryFilters\ContactFilter;
use App\Models\Contact;
use App\Repositories\ContactRepositoryInterface;
use App\Services\ContactServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class ContactService implements ContactServiceInterface
{
    public function __construct(private ContactRepositoryInterface $contactRepository)
    {
    }

    public function getAll(ContactFilter $filter = null): LengthAwarePaginator
    {
        return $this->contactRepository->getAll($filter);
    }

    public function create(array $data): Contact
    {
        return $this->contactRepository->create($data);
    }

    public function getOne(int $id): Contact
    {
        return $this->contactRepository->getById($id);
    }

    public function update(Contact $contact, array $data): bool
    {
        return $this->contactRepository->update($contact, $data);
    }

    public function delete(Contact $contact): bool
    {
        return $this->contactRepository->delete($contact);
    }

    public function pluck(string $value, string $key = null, ContactFilter $filter = null): Collection
    {
        return $this->contactRepository->pluck($value, $key, $filter);
    }
}
