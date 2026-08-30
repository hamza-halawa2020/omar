<?php

namespace App\Services;

use App\Models\Association;
use App\Models\AssociationPayment;
use App\Models\Client;
use App\Models\PaymentWay;
use App\Models\Transaction;
use App\Services\Concerns\HandlesTransactionConcurrency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppService;

class AssociationService
{
    use HandlesTransactionConcurrency;

    public function indexData(): array
    {
        $clients = Client::where('type', 'client')->get();

        return compact('clients');
    }

    public function list(): Collection
    {
        return Association::with([
            'members.client' => function ($query) {
                $query->where('type', 'client');
            },
            'creator'
        ])->get();
    }

    public function details(int $id): array
    {
        $association = Association::with(['members.client', 'creator'])->findOrFail($id);
        $clients = Client::where('type', 'client')->get();
        $paymentWays = PaymentWay::all();

        return compact('association', 'clients', 'paymentWays');
    }

    public function addMember(int $id, array $data): mixed
    {
        $association = Association::findOrFail($id);
        $members = $association->members()->orderBy('payout_order')->get();
        $lastOrder = $members->max('payout_order') ?? 0;

        if ($members->isEmpty()) {
            $receiveDate = Carbon::parse($association->start_date);
        } else {
            $lastReceiveDate = Carbon::parse($members->last()->receive_date);
            $receiveDate = $lastReceiveDate->copy()->addDays((int) $association->per_day);
        }

        $member = $association->members()->create([
            'client_id' => $data['client_id'],
            'payout_order' => $lastOrder + 1,
            'receive_date' => $receiveDate,
        ]);

        $newTotal = $association->members()->count();
        $association->update([
            'total_members' => $newTotal,
            'end_date' => Carbon::parse($association->start_date)->addDays(($newTotal - 1) * (int) $association->per_day),
        ]);

        return $member->load('client');
    }

    public function store(array $data): Association
    {
        $data['created_by'] = Auth::id();
        $memberCount = count($data['total_members']);
        $endDate = Carbon::parse($data['start_date'])->addDays(($memberCount - 1) * $data['per_day']);

        $association = DB::transaction(function () use ($data, $endDate, $memberCount) {
            $association = Association::create([
                'name' => $data['name'],
                'per_day' => $data['per_day'],
                'monthly_amount' => $data['monthly_amount'],
                'start_date' => $data['start_date'],
                'end_date' => $endDate,
                'total_members' => $memberCount,
                'created_by' => $data['created_by'],
            ]);

            $startDate = Carbon::parse($data['start_date']);
            foreach ($data['total_members'] as $index => $clientId) {
                $receiveDate = $startDate->copy()->addDays($index * $data['per_day']);
                $association->members()->create([
                    'client_id' => is_array($clientId) ? $clientId[0] : $clientId,
                    'payout_order' => $index + 1,
                    'receive_date' => $receiveDate,
                ]);
            }

            return $association;
        });

        return $association->load('members.client');
    }

    public function deleteMember(int $associationId, int $memberId): void
    {
        $association = Association::findOrFail($associationId);
        $member = $association->members()->findOrFail($memberId);

        if ($member->payments()->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.cannot_delete_member_with_payments')], 400));
        }

        DB::transaction(function () use ($association, $member) {
            $member->delete();

            $members = $association->members()->orderBy('payout_order')->get();
            $startDate = Carbon::parse($association->start_date);
            $perDay = (int) $association->per_day;

            foreach ($members as $index => $m) {
                $m->update([
                    'payout_order' => $index + 1,
                    'receive_date' => $startDate->copy()->addDays($index * $perDay),
                ]);
            }

            $newTotal = $members->count();
            $association->update([
                'total_members' => $newTotal,
                'end_date' => $startDate->copy()->addDays(($newTotal - 1) * $perDay),
            ]);
        });
    }

    public function addPayment(int $id, array $data): array
    {
        $association = Association::findOrFail($id);
        $member = $association->members()->findOrFail($data['member_id']);

        $totalDue = $association->total_members * $association->monthly_amount;
        $totalPaid = $member->payments()->sum('amount');
        $newAmount = $data['amount'];
        if ($totalPaid + $newAmount > $totalDue) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.amount_exceeds_total', ['max' => $totalDue - $totalPaid])], 400));
        }

        $data['association_id'] = $id;
        $data['created_by'] = Auth::id();
        $total = $data['amount'] + ($data['commission'] ?? 0);

        return $this->withTransactionSubmissionLock('association-payment', $data, function () use ($association, $member, $data, $total) {
            return DB::transaction(function () use ($association, $member, $data, $total) {
                $member = $member->newQuery()->with('client')->whereKey($member->id)->lockForUpdate()->firstOrFail();
                $totalDue = $association->total_members * $association->monthly_amount;
                $totalPaid = $member->payments()->sum('amount');
                if (($totalPaid + $data['amount']) > $totalDue) {
                    throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.amount_exceeds_total', ['max' => $totalDue - $totalPaid])], 400));
                }

                $paymentWay = $this->lockedPaymentWay($data['payment_way_id']);
                $monthlyLimit = $this->lockedCurrentMonthlyLimit($paymentWay);
                $this->assertPaymentWayCanHandleTransaction($paymentWay, $monthlyLimit, 'receive', $data['amount'], $total);

                $transaction = Transaction::create([
                    'payment_way_id' => $paymentWay->id,
                    'created_by' => Auth::id(),
                    'type' => 'receive',
                    'amount' => $data['amount'],
                    'commission' => $data['commission'] ?? 0,
                    'notes' => __('messages.payment_for_installment') . ' ' . ($member->client->name ?? '') . ' - ' . $association->name,
                    'client_id' => $member->client_id ?? null,
                    'balance_before_transaction' => $paymentWay->balance,
                    'balance_after_transaction' => $paymentWay->balance + $total,
                ]);

                $paymentWay->increment('balance', $total);
                if ($monthlyLimit) {
                    $monthlyLimit->increment('receive_used', $total);
                }

                $data['transaction_id'] = $transaction->id;
                $payment = AssociationPayment::create($data);

                $transaction->logs()->create([
                    'created_by' => Auth::id(),
                    'action' => 'create',
                    'data' => [
                        'association' => ['id' => $association->id, 'name' => $association->name],
                        'member' => [
                            'id' => $member->id,
                            'name' => $member->client->name ?? null,
                            'total_paid' => $member->payments()->sum('amount'),
                        ],
                        'payment_way' => [
                            'id' => $paymentWay->id,
                            'name' => $paymentWay->name,
                            'balance_before' => $transaction->balance_before_transaction,
                            'balance_after' => $transaction->balance_after_transaction,
                        ],
                    ],
                ]);

                if ($member->client) {
                    app(WhatsAppService::class)->sendTransactionMessage($member->client, $data['amount'], 'receive', ['association' => $association->name]);
                }

                return ['payment' => $payment, 'transaction' => $transaction];
            });
        });
    }

    public function payMember(int $id, array $data): array
    {
        $association = Association::with('members.client')->findOrFail($id);
        $member = $association->members()->findOrFail($data['member_id']);

        if ($member->has_received) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.this_member_recevied')], 400));
        }

        $totalReceived = $association->monthly_amount * $association->members()->count();
        $commission = $data['commission'] ?? 0;
        $total = $totalReceived + $commission;

        return $this->withTransactionSubmissionLock('association-member-payout', $data, function () use ($data, $member, $association, $totalReceived, $commission, $total) {
            return DB::transaction(function () use ($data, $member, $association, $totalReceived, $commission, $total) {
                $member = $member->newQuery()->with('client')->whereKey($member->id)->lockForUpdate()->firstOrFail();
                if ($member->has_received) {
                    throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.this_member_recevied')], 400));
                }

                $paymentWay = $this->lockedPaymentWay($data['payment_way_id']);
                $monthlyLimit = $this->lockedCurrentMonthlyLimit($paymentWay);
                $this->assertPaymentWayCanHandleTransaction($paymentWay, $monthlyLimit, 'send', $totalReceived, $total);

                $transaction = Transaction::create([
                    'payment_way_id' => $paymentWay->id,
                    'created_by' => Auth::id(),
                    'type' => 'send',
                    'amount' => $totalReceived,
                    'commission' => $commission,
                    'notes' => __('messages.recevied_done') . ' ' . $association->name . ' - ' . ($member->client->name ?? ''),
                    'client_id' => $member->client_id,
                    'balance_before_transaction' => $paymentWay->balance,
                    'balance_after_transaction' => $paymentWay->balance - $total,
                ]);

                $paymentWay->decrement('balance', $total);
                if ($monthlyLimit) {
                    $monthlyLimit->increment('send_used', $total);
                }

                $member->update([
                    'has_received' => true,
                    'transaction_id' => $transaction->id,
                    'amount' => $totalReceived,
                    'received_at' => now(),
                ]);

                $transaction->logs()->create([
                    'created_by' => Auth::id(),
                    'action' => 'create',
                    'data' => [
                        'association' => ['id' => $association->id, 'name' => $association->name],
                        'member' => [
                            'id' => $member->id,
                            'name' => $member->client->name ?? null,
                            'amount_received' => $totalReceived,
                        ],
                        'payment_way' => [
                            'id' => $paymentWay->id,
                            'name' => $paymentWay->name,
                            'balance_before' => $transaction->balance_before_transaction,
                            'balance_after' => $transaction->balance_after_transaction,
                        ],
                    ],
                ]);

                if ($member->client) {
                    app(WhatsAppService::class)->sendTransactionMessage($member->client, $totalReceived, 'send', ['association_payout' => $association->name]);
                }

                return ['member' => $member->fresh(), 'transaction' => $transaction];
            });
        });
    }

    public function show(int $id): Association
    {
        return Association::with(['members.client', 'payments', 'creator'])->findOrFail($id);
    }

    public function update(int $id, array $data): Association
    {
        $association = Association::findOrFail($id);
        $association->update($data);

        return $association;
    }

    public function destroy(int $id): void
    {
        $association = Association::findOrFail($id);
        if ($association->members()->exists() || $association->payments()->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.cannot_delete_association_with_data')], 400));
        }
        $association->delete();
    }
}
