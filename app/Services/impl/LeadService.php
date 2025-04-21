<?php

namespace App\Services\impl;

use App\DTO\QueryFilters\LeadFilter;
use App\Jobs\SendLeadCreationReminder;
use App\Models\Lead;
use App\Repositories\LeadRepositoryInterface;
use App\Services\AccountServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\LeadServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

readonly class LeadService implements LeadServiceInterface
{

    public function __construct(
        private LeadRepositoryInterface $leadRepository,
        private AccountServiceInterface $accountService,
        private ContactServiceInterface $contactService,
    )
    {
    }

    public function getAll(LeadFilter $filter = null): LengthAwarePaginator
    {
        return $this->leadRepository->getAll($filter);
    }

    public function create(array $data): Lead
    {
        $lead = $this->leadRepository->create($data);

        SendLeadCreationReminder::dispatch($lead->id)
            ->delay(
                now()->addHours(
                    config('lead_reminders.interval_hours')
                )
            );

        return $lead;
    }

    public function getOne(int $id, LeadFilter $filter = null): Lead
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
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'industry' => $lead->industry,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'address' => $lead->address,
                    'assigned_to' => $lead->assigned_to,
                ]);

                $this->contactService->create([
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
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


    public function pluck(string $value, string $key = null, LeadFilter $filter = null): Collection
    {
        return $this->leadRepository->pluck($value, $key, $filter);
    }
}
