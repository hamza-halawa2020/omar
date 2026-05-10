<?php

namespace App\Services\Concerns;

use App\Models\PaymentWay;

trait BuildsPaymentWayLogData
{
    protected function paymentWayLogData(PaymentWay $paymentWay): array
    {
        return [
            'id' => $paymentWay->id,
            'name' => $paymentWay->name,
            'category' => optional($paymentWay->category)->name,
            'category_id' => $paymentWay->category_id,
            'sub_category' => optional($paymentWay->subCategory)->name,
            'sub_category_id' => $paymentWay->sub_category_id,
            'type' => $paymentWay->type,
            'phone_number' => $paymentWay->phone_number,
            'send_limit' => $paymentWay->send_limit,
            'receive_limit' => $paymentWay->receive_limit,
            'balance' => $paymentWay->balance,
            'creator' => optional($paymentWay->creator)->name,
            'created_by' => $paymentWay->created_by,
        ];
    }
}
