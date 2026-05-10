<?php

namespace App\Services\Concerns;

use App\Models\PaymentWay;
use App\Models\PaymentWayLimit;

trait HandlesWalletMonthlyLimits
{
    protected function findCurrentMonthlyLimit(PaymentWay $paymentWay): ?PaymentWayLimit
    {
        if ($paymentWay->type !== 'wallet') {
            return null;
        }

        return $paymentWay->monthlyLimits()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->first();
    }

    protected function getOrCreateCurrentMonthlyLimit(PaymentWay $paymentWay): ?PaymentWayLimit
    {
        $monthlyLimit = $this->findCurrentMonthlyLimit($paymentWay);
        if ($paymentWay->type !== 'wallet') {
            return null;
        }

        if (! $monthlyLimit) {
            $monthlyLimit = $paymentWay->monthlyLimits()->create([
                'month' => now()->month,
                'year' => now()->year,
                'send_limit' => $paymentWay->send_limit,
                'receive_limit' => $paymentWay->receive_limit,
                'send_used' => 0,
                'receive_used' => 0,
            ]);
        }

        return $monthlyLimit;
    }
}
