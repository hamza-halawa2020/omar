<?php

namespace App\Repositories\impl;

use App\DTO\ContactFilter;
use App\Models\Contact;
use App\Repositories\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function getAll(ContactFilter $filter = null): LengthAwarePaginator
    {
        return Contact::query()
            ->when($filter->withAccount, function ($query) use ($filter) {
                return $query->withAccount();
            })
            ->latest()->paginate();
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

    public function pluck(string $value, string $key = null): Collection
    {
        return $key
            ? Contact::pluck($value, $key)
            : Contact::pluck($value);
    }

}
