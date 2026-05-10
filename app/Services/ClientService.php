<?php

namespace App\Services;

use App\Models\Client;
use App\Models\PaymentWay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function list(Request $request): Collection
    {
        $query = Client::query();

        $query->when($request->type === 'merchant', function ($q) {
            return $q->where('type', 'merchant');
        });

        $query->with(['creator'])->orderByDesc('debt');
        $this->applySearch($query, $request->search);

        return $query->get();
    }

    public function listDebts(?string $search): Collection
    {
        $query = Client::where('type', 'client')
            ->where('debt', '>', 0)
            ->whereDoesntHave('installmentContracts')
            ->with(['creator', 'installmentContracts'])
            ->orderByDesc('debt');

        $this->applySearch($query, $search);

        return $query->get();
    }

    public function listMerchants(?string $search): Collection
    {
        $query = Client::where('type', 'merchant')
            ->with(['creator'])
            ->orderByDesc('debt');

        $this->applySearch($query, $search);

        return $query->get();
    }

    public function listCreditor(?string $search): Collection
    {
        $query = Client::where('type', 'client')
            ->where('debt', '<', 0)
            ->with(['creator', 'installmentContracts'])
            ->orderBy('debt', 'asc');

        $this->applySearch($query, $search);

        return $query->get();
    }

    public function listClientInstallments(?string $search): Collection
    {
        $query = Client::where('type', 'client')
            ->where('debt', '!=', 0)
            ->whereHas('installmentContracts')
            ->with(['creator', 'installmentContracts'])
            ->orderByDesc('debt');

        $this->applySearch($query, $search);

        return $query->get();
    }

    public function store(array $data): Client
    {
        $data['created_by'] = Auth::id();

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
            'debtLogs.source',
            'debtLogs.creator',
        ])->findOrFail($id);

        return [
            'client' => $client,
            'remaining_amount' => $client->total_remaining_amount,
            'remaining_installments' => $client->total_remaining_installments,
            'paymentWays' => PaymentWay::all(),
        ];
    }

    public function update(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);
        $this->guardClientHasNoRelations($client, __('messages.cannot_update_client_with_transactions'));

        $client->log_description = __('messages.manual_update');
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
                ->orWhere('phone_number', 'like', "%{$search}%");
        });
    }

    private function guardClientHasNoRelations(Client $client, string $message): void
    {
        if ($client->transactions()->exists() || $client->installmentContracts()->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => $message], 400));
        }
    }
}
