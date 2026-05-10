<?php

namespace App\Services;

use App\Models\PaymentWayLog;
use Illuminate\Database\Eloquent\Collection;

class PaymentWayLogService
{
    public function list(): Collection
    {
        return PaymentWayLog::with(['paymentWay', 'creator'])->latest()->get();
    }
}
