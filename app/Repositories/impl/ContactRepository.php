<?php

namespace App\Repositories\impl;

use App\DTO\QueryFilters\ContactFilter;
use App\Models\Contact;
use App\Repositories\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function getAll(ContactFilter $filter = null): LengthAwarePaginator
    {
        return Contact::query()
            ->when($filter?->withAccount, function ($query) use ($filter) {
                return $query->withAccount();
            })
            ->when($filter?->accountId, function ($query) use ($filter) {
                return $query->where('account_id', $filter?->accountId);
            })
            ->latest()
            ->paginate($filter?->perPage ?? 10, ['*'], 'contacts_page')
            ->appends(preserveOtherPagination('contacts_page'));
    }

    public function create(array $data): Contact
    {
        return Contact::create($data);
    }

    public function getById(int $id): Contact
    {
        return Contact::findOrFail($id);
    }

    public function update(Contact $contact, array $data): bool
    {
        return $contact->update($data);
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }

    public function pluck(string $value, string $key = null, ContactFilter $filter = null): Collection
    {
        $query = Contact::query()
            ->when($filter?->withAccount, function ($query) use ($filter) {
                return $query->withAccount();
            })
            ->when($filter?->accountId, function ($query) use ($filter) {
                return $query->where('account_id', $filter?->accountId);
            });

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }

}
