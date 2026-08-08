<?php

namespace App\Services\Concerns;

use App\Models\PaymentWay;
use App\Models\PaymentWayLimit;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;

trait HandlesTransactionConcurrency
{
    protected function rejectDuplicateTransactionSubmission(string $scope, array $payload, int $seconds = 5): void
    {
        unset($payload['created_by'], $payload['attachment']);

        $key = 'transaction-submission:'.sha1($scope.'|'.$this->stableJson($payload));

        if (! Cache::lock($key, $seconds)->get()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => __('messages.duplicate_transaction_in_progress'),
            ], 409));
        }
    }

    protected function lockedPaymentWay(int|string $paymentWayId): PaymentWay
    {
        return PaymentWay::query()->whereKey($paymentWayId)->lockForUpdate()->firstOrFail();
    }

    protected function lockedCurrentMonthlyLimit(PaymentWay $paymentWay): ?PaymentWayLimit
    {
        if ($paymentWay->type !== 'wallet') {
            return null;
        }

        $monthlyLimit = $paymentWay->monthlyLimits()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->lockForUpdate()
            ->first();

        if ($monthlyLimit) {
            return $monthlyLimit;
        }

        return $paymentWay->monthlyLimits()->create([
            'month' => now()->month,
            'year' => now()->year,
            'send_limit' => $paymentWay->send_limit,
            'receive_limit' => $paymentWay->receive_limit,
            'send_used' => 0,
            'receive_used' => 0,
        ]);
    }

    protected function assertPaymentWayCanHandleTransaction(PaymentWay $paymentWay, ?PaymentWayLimit $monthlyLimit, string $type, float|int $amount, float|int $total): void
    {
        if ($type === 'send' && $total > $paymentWay->balance) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.not_enough_balance')], 400));
        }

        if (! $monthlyLimit) {
            return;
        }

        if ($type === 'send' && ($monthlyLimit->send_used + $amount) > $monthlyLimit->send_limit) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.send_limit_exceeded')], 400));
        }

        if ($type === 'receive' && ($monthlyLimit->receive_used + $total) > $monthlyLimit->receive_limit) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.receive_limit_exceeded')], 400));
        }
    }

    protected function withTransactionSubmissionLock(string $scope, array $payload, Closure $callback): mixed
    {
        $this->rejectDuplicateTransactionSubmission($scope, $payload);

        return $callback();
    }

    private function stableJson(array $payload): string
    {
        $this->ksortRecursive($payload);

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function ksortRecursive(array &$payload): void
    {
        ksort($payload);

        foreach ($payload as &$value) {
            if (is_array($value)) {
                $this->ksortRecursive($value);
            }
        }
    }
}
