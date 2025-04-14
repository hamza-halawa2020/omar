<?php

namespace App\Services\impl;

use App\DTO\LeadFilter;
use App\Models\Account;
use App\Models\Lead;
use App\Repositories\ContactRepositoryInterface;
use App\Repositories\LeadRepositoryInterface;
use App\Services\AccountServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\LeadServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeadService implements LeadServiceInterface
{

    public function __construct(
        private readonly LeadRepositoryInterface $leadRepository,
        private readonly AccountServiceInterface $accountService,
        private readonly ContactServiceInterface $contactService,
    )
    {
    }

    public function getAll(LeadFilter $filter): LengthAwarePaginator
    {
        return $this->leadRepository->getAll($filter);
    }

    public function create(array $data): Lead
    {
        return $this->leadRepository->create($data);
    }

    public function getOne(int $id): Lead
    {
        return $this->leadRepository->getById($id);
    }

    public function update(Lead $lead, array $data): bool
    {
        return $this->leadRepository->update($lead, $data);
    }

    public function delete(Lead $lead): bool
    {
        return $this->leadRepository->delete($lead);
    }

    public function convertIntoAccountAndContact(Lead $lead): bool
    {
        try {
            DB::transaction(function () use ($lead) {
                $account = $this->accountService->create([
                    'name' => $lead->name,
                    'industry' => $lead->industry,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'address' => $lead->address,
                    'assigned_to' => $lead->assigned_to,
                ]);

                $this->contactService->create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'position' => $lead->position,
                    'account_id' => $account->id,
                ]);
            });

            $lead->update([
                'is_converted' => true,
            ]);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }


}
