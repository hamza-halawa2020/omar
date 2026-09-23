<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class ClientService
{
    public function list(Request $request): Collection|LengthAwarePaginator
    {
        $query = $this->clientListQuery();

        $query->when($request->type === 'merchant', function ($q) {
            return $q->where('type', 'merchant');
        });

        $query->orderByDesc('debt');
        $this->applySearch($query, $request->search);

        if ($request->has('page')) {
            $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

            return $query->paginate($perPage);
        }

        return $query
            ->limit(min(max((int) $request->input('limit', 60), 1), 100))
            ->get();
    }

    public function listDebts(Request $request): Collection|LengthAwarePaginator
    {
        $query = $this->clientListQuery()
            ->where('type', 'client')
            ->where('debt', '>', 0)
            ->whereDoesntHave('installmentContracts')
            ->orderByDesc('debt');

        $this->applySearch($query, $request->search);

        return $this->paginateOrGet($query, $request);
    }

    public function listMerchants(Request $request): Collection|LengthAwarePaginator
    {
        $query = $this->clientListQuery()
            ->where('type', 'merchant')
            ->orderByDesc('debt');

        $this->applySearch($query, $request->search);

        return $this->paginateOrGet($query, $request);
    }

    public function listCreditor(Request $request): Collection|LengthAwarePaginator
    {
        $query = $this->clientListQuery()
            ->where('type', 'client')
            ->where('debt', '<', 0)
            ->orderBy('debt', 'asc');

        $this->applySearch($query, $request->search);

        return $this->paginateOrGet($query, $request);
    }

    public function listClientInstallments(Request $request): Collection|LengthAwarePaginator
    {
        $query = $this->clientListQuery()
            ->where('type', 'client')
            ->where('debt', '!=', 0)
            ->whereHas('installmentContracts')
            ->orderByDesc('debt');

        $this->applySearch($query, $request->search);

        return $this->paginateOrGet($query, $request);
    }

    private function paginateOrGet(Builder $query, Request $request): Collection|LengthAwarePaginator
    {
        if ($request->has('page')) {
            $perPage = min(max((int) $request->input('per_page', 25), 1), 100);

            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function store(array $data): Client
    {
        $data['created_by'] = Auth::id();
        $data = $this->normalizePhoneData($data);

        return Client::create($data);
    }

    public function show(int $id): Client
    {
        return Client::with(['creator', 'transactions'])->findOrFail($id);
    }

    public function showPage(int $id): array
    {
        $client = Client::with([
            'creator',
            'transactions.paymentWay',
            'transactions.debtLog',
            'installmentContracts.installments.payments',
            'debtLogs.creator',
        ])->findOrFail($id);

        return [
            'client' => $client,
            'remaining_amount' => $client->total_remaining_amount,
            'remaining_installments' => $client->total_remaining_installments,
        ];
    }

    public function update(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);
        $this->guardClientHasNoRelations($client, __('messages.cannot_update_client_with_transactions'));

        $client->log_description = __('messages.manual_update');
        $data = $this->normalizePhoneData($data);
        $client->update($data);

        return $client;
    }

    public function destroy(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->guardClientHasNoRelations($client, __('messages.cannot_delete_client_with_transactions'));
        $client->delete();
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        if (! $search) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('country_code', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");
        });
    }

    private function clientListQuery(): Builder
    {
        return Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.type',
                'clients.phone_number',
                'clients.country_code',
                'clients.debt',
                'clients.created_by',
                'clients.created_at',
                'clients.updated_at',
            ])
            ->addSelect([
                'installment_remaining_amount' => DB::table('installment_contracts')
                    ->join('installments', 'installments.installment_contract_id', '=', 'installment_contracts.id')
                    ->selectRaw('COALESCE(SUM(installments.required_amount - installments.paid_amount), 0)')
                    ->whereColumn('installment_contracts.client_id', 'clients.id'),
            ])
            ->with(['creator:id,name,email']);
    }

    private function normalizePhoneData(array $data): array
    {
        if (empty($data['phone_number'])) {
            $data['phone_number'] = null;
            $data['country_code'] = null;

            return $data;
        }

        $countryCode = $data['country_code'] ?? '+20';
        $digitsOnlyCountryCode = preg_replace('/\D+/', '', $countryCode);
        $digitsOnlyPhoneNumber = preg_replace('/\D+/', '', $data['phone_number']);

        if (! $digitsOnlyCountryCode || ! $digitsOnlyPhoneNumber) {
            throw ValidationException::withMessages([
                'phone_number' => __('validation.regex', ['attribute' => __('messages.phone_number')]),
            ]);
        }

        if (strlen($digitsOnlyPhoneNumber) < 6) {
            throw ValidationException::withMessages([
                'phone_number' => __('validation.regex', ['attribute' => __('messages.phone_number')]),
            ]);
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $phoneUtil->parse('+'.$digitsOnlyCountryCode.$digitsOnlyPhoneNumber, null);

            if ($phoneUtil->isValidNumber($phoneNumber)) {
                $data['country_code'] = '+'.$phoneNumber->getCountryCode();
                $data['phone_number'] = $phoneUtil->getNationalSignificantNumber($phoneNumber);

                return $data;
            }
        } catch (NumberParseException) {
            // Keep flexible CRM entry for manually entered local numbers.
        }

        $data['country_code'] = '+'.$digitsOnlyCountryCode;
        $data['phone_number'] = $digitsOnlyPhoneNumber;

        return $data;
    }

    private function guardClientHasNoRelations(Client $client, string $message): void
    {
        if ($client->transactions()->exists() || $client->installmentContracts()->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => $message], 400));
        }
    }
}
